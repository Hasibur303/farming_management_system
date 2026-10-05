<?php
require_once dirname(__DIR__) . '/security/bootstrap.php';
require_role('Admin');
header('Cache-Control: no-store');
$alerts = [];
foreach (glob(dirname(__DIR__) . '/storage/activity/*.json') ?: [] as $file) {
    $handle = fopen($file, 'r');
    if (!$handle) continue;
    if (flock($handle, LOCK_SH)) {
        $records = json_decode(stream_get_contents($handle), true);
        foreach (is_array($records) ? $records : [] as $entry) {
            if ($entry['time'] > time() - 86400 && $entry['reason'] !== null) $alerts[] = $entry;
        }
        flock($handle, LOCK_UN);
    }
    fclose($handle);
}
usort($alerts, static fn($a, $b) => $b['time'] <=> $a['time']);
$alerts = array_slice($alerts, 0, 200);
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>SmartKrishi Security Activity</title>
<style>body{font:16px Arial;background:#f0f6f1;color:#17351f;margin:32px}main{max-width:1200px;margin:auto}table{width:100%;border-collapse:collapse;background:white}th,td{padding:12px;text-align:left;border-bottom:1px solid #ddd}a{color:#237a39}.scroll{overflow:auto}small{display:block;color:#555}td{overflow-wrap:anywhere}</style></head><body><main>
<a href="admin.php">Back to Admin dashboard</a><h1>Security activity</h1>
<p>Latest <?= count($alerts) ?> alerts from the last 24 hours (maximum 200). Alerts are signals for review, not proof of malicious intent.</p>
<p>AI quota: 10 attempts / 10 minutes and 50 accepted attempts / rolling 24 hours. Price changes of 50% or more, 10 price/order changes, or 5 denied accesses within 10 minutes trigger review alerts.</p>
<div class="scroll"><table><thead><tr><th>Time (UTC)</th><th>Account</th><th>Event / details</th><th>Reason</th><th>Response</th></tr></thead><tbody>
<?php foreach ($alerts as $entry): ?><tr><td><?= e(gmdate('Y-m-d H:i:s', $entry['time'])) ?></td><td><?= e($entry['actor']) ?></td><td><?= e($entry['event']) ?><small><?= e(json_encode($entry['context'], JSON_UNESCAPED_SLASHES)) ?></small></td><td><?= e($entry['reason']) ?></td><td><?= $entry['action'] === 'block' ? 'AI request blocked' : 'Review only; existing permissions remain enforced' ?></td></tr><?php endforeach; ?>
<?php if (!$alerts): ?><tr><td colspan="5">No alerts in the current window.</td></tr><?php endif; ?>
</tbody></table></div></main></body></html>
