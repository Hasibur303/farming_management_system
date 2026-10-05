#Requires -RunAsAdministrator
[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'

$configPath = 'C:\Program Files (x86)\ossec-agent\ossec.conf'
$agentLogPath = 'C:\Program Files (x86)\ossec-agent\ossec.log'
$securityLogPath = 'D:\farming_management_system\storage\logs\security.jsonl'
$expectedLocation = '<location>D:\farming_management_system\storage\logs\security.jsonl</location>'

Write-Host '=== Wazuh service ===' -ForegroundColor Cyan
Get-Service -Name WazuhSvc | Format-Table Name, Status, StartType -AutoSize

Write-Host '=== SmartKrishi source in agent configuration ===' -ForegroundColor Cyan
$config = Get-Content -LiteralPath $configPath -Raw
if (-not $config.Contains($expectedLocation)) {
    throw 'SmartKrishi security log location is missing from ossec.conf.'
}
Write-Host 'Configuration entry found.' -ForegroundColor Green

$testId = [Guid]::NewGuid().ToString('N')
$event = [ordered]@{
    timestamp   = (Get-Date).ToUniversalTime().ToString('o')
    application = 'smartkrishi'
    level       = 'warning'
    event       = 'security.wazuh_test'
    user_id     = $null
    role        = $null
    ip          = '127.0.0.1'
    method      = 'TEST'
    path        = '/security/wazuh-test'
    context     = @{ test_id = $testId }
} | ConvertTo-Json -Compress

$eventBytes = [Text.UTF8Encoding]::new($false).GetBytes($event + [Environment]::NewLine)
$stream = [IO.FileStream]::new(
    $securityLogPath,
    [IO.FileMode]::Append,
    [IO.FileAccess]::Write,
    [IO.FileShare]::ReadWrite
)
try {
    $stream.Write($eventBytes, 0, $eventBytes.Length)
    $stream.Flush()
} finally {
    $stream.Dispose()
}
Write-Host '=== Fresh JSON event written ===' -ForegroundColor Cyan
Write-Host "Test ID: $testId"
Write-Host $event

Start-Sleep -Seconds 3

Write-Host '=== Recent agent messages related to log collection ===' -ForegroundColor Cyan
$matches = Select-String -LiteralPath $agentLogPath -Pattern 'security.jsonl|logcollector|ERROR|WARNING' | Select-Object -Last 40
if ($matches) {
    $matches | ForEach-Object { $_.Line }
} else {
    Write-Host 'No matching errors or logcollector messages were found.'
}

Write-Host 'Wait 1-3 minutes, then search Wazuh Threat Hunting for: rule.id: 100100' -ForegroundColor Yellow
