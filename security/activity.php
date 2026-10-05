<?php
declare(strict_types=1);

// Called after bootstrap; independent of the business database and its transactions.
function activity_rules(string $event, array $context, array $history, int $now): array
{
    $count = 1;
    foreach ($history as $entry) {
        if ($entry['event'] === $event && $entry['time'] > $now - 600) $count++;
    }
    $reason = null;
    $action = 'observe';
    if ($event === 'authorization.ownership_denied' || $event === 'authorization.role_denied') {
        if ($count >= 5) $reason = "Repeated denied access: {$count} attempts in 10 minutes.";
    } elseif ($event === 'market.price_changed') {
        $old = (float) ($context['old_price'] ?? 0);
        $new = (float) ($context['new_price'] ?? 0);
        if ($old > 0 && abs($new - $old) / $old >= 0.5) $reason = 'Product price changed by at least 50%; review may be needed.';
        if ($count >= 10) $reason = "Frequent price changes: {$count} in 10 minutes.";
    } elseif ($event === 'market.order_status_changed' && $count >= 10) {
        $reason = "Frequent order status changes: {$count} in 10 minutes.";
    } elseif ($event === 'ai.request') {
        $daily = 1;
        foreach ($history as $entry) {
            if ($entry['event'] === $event && $entry['time'] > $now - 86400 && ($entry['action'] ?? '') !== 'block') $daily++;
        }
        if ($count > 10 || $daily > 50) {
            $reason = 'AI quota exceeded: maximum 10 attempts per 10 minutes and 50 accepted attempts per rolling 24 hours, shared across AI tools.';
            $action = 'block';
        }
    }
    return ['reason' => $reason, 'action' => $action, 'count' => $count];
}

function activity_record(string $event, array $context = []): array
{
    $allowed = ['authorization.ownership_denied', 'authorization.role_denied', 'market.price_changed', 'market.order_status_changed', 'ai.request'];
    if (!in_array($event, $allowed, true)) return ['action' => 'observe', 'reason' => null];
    $directory = dirname(__DIR__) . '/storage/activity';
    if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) throw new RuntimeException('Activity storage unavailable.');
    $actor = isset($_SESSION['user_id']) ? 'user:' . (int) $_SESSION['user_id'] : 'ip:' . security_client_ip();
    $handle = fopen($directory . '/' . hash('sha256', $actor) . '.json', 'c+');
    if (!$handle) throw new RuntimeException('Activity storage unavailable.');
    try {
        if (!flock($handle, LOCK_EX)) throw new RuntimeException('Activity lock unavailable.');
        $raw = stream_get_contents($handle);
        $entries = $raw === '' ? [] : json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        $now = time();
        $entries = array_values(array_filter($entries, static fn($entry) => $entry['time'] > $now - 86400));
        $decision = activity_rules($event, $context, $entries, $now);
        $entry = ['time' => $now, 'event' => $event, 'actor' => $actor, 'context' => $context] + $decision;
        // Keep quota evidence bounded: repeated blocked requests are represented by the latest entry.
        if ($decision['action'] === 'block') {
            $entries = array_values(array_filter($entries, static fn($item) => !($item['event'] === 'ai.request' && $item['action'] === 'block')));
        }
        $entries[] = $entry;
        $encoded = json_encode($entries, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
        rewind($handle);
        if (fwrite($handle, $encoded) !== strlen($encoded) || !ftruncate($handle, strlen($encoded)) || !fflush($handle)) throw new RuntimeException('Activity write failed.');
        return $entry;
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

function activity_ai_guard(string $tool): void
{
    try {
        $decision = activity_record('ai.request', ['tool' => $tool]);
    } catch (Throwable $exception) {
        error_log('SmartKrishi activity storage unavailable');
        http_response_code(503);
        exit('AI tools are temporarily unavailable. Please try again later.');
    }
    if ($decision['action'] === 'block') {
        security_log('activity.ai_blocked', ['reason' => $decision['reason']]);
        http_response_code(429);
        header('Retry-After: 600');
        exit('AI usage limit reached. Please try again later; the daily quota resets as older requests expire.');
    }
}
