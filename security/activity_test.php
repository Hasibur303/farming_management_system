<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/activity.php';
$now = 200000;
$checks = 0;
function check_activity(bool $ok): void { global $checks; $checks++; if (!$ok) throw new RuntimeException('Activity test failed: ' . $checks); }
check_activity(activity_rules('market.price_changed', ['old_price'=>100, 'new_price'=>110], [], $now)['reason'] === null);
check_activity(activity_rules('market.price_changed', ['old_price'=>100, 'new_price'=>40], [], $now)['reason'] !== null);
$history = array_fill(0, 10, ['event'=>'ai.request', 'time'=>$now-1, 'action'=>'observe']);
check_activity(activity_rules('ai.request', [], array_slice($history, 0, 9), $now)['action'] === 'observe');
check_activity(activity_rules('ai.request', [], $history, $now)['action'] === 'block');
check_activity(activity_rules('ai.request', [], $history, $now+86401)['action'] === 'observe');
$daily = array_fill(0, 50, ['event'=>'ai.request', 'time'=>$now-1000, 'action'=>'observe']);
check_activity(activity_rules('ai.request', [], $daily, $now)['action'] === 'block');
$denials = array_fill(0, 4, ['event'=>'authorization.ownership_denied', 'time'=>$now-1]);
check_activity(activity_rules('authorization.ownership_denied', [], $denials, $now)['reason'] !== null);
check_activity(activity_rules('authorization.ownership_denied', [], $denials, $now+601)['reason'] === null);
$orders = array_fill(0, 9, ['event'=>'market.order_status_changed', 'time'=>$now-1]);
check_activity(activity_rules('market.order_status_changed', [], $orders, $now)['reason'] !== null);
echo "PASS: $checks detection, boundary and expiry checks\n";
function security_client_ip(): string { return 'test'; }
$_SESSION['user_id'] = random_int(100000000, 200000000);
$testPath = dirname(__DIR__) . '/storage/activity/' . hash('sha256', 'user:' . $_SESSION['user_id']) . '.json';
if (file_exists($testPath)) throw new RuntimeException('Test actor collision');
try {
    for ($i = 0; $i < 10; $i++) check_activity(activity_record('ai.request', ['tool'=>'test'])['action'] === 'observe');
    check_activity(activity_record('ai.request', ['tool'=>'test'])['action'] === 'block');
    check_activity(activity_record('ai.request', ['tool'=>'test'])['action'] === 'block');
    echo "PASS: persisted quota and repeated blocking\n";
} finally {
    if (is_file($testPath)) unlink($testPath);
}
