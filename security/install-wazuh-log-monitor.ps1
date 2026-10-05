#Requires -RunAsAdministrator
[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'

$agentDirectory = 'C:\Program Files (x86)\ossec-agent'
$configPath = Join-Path $agentDirectory 'ossec.conf'
$securityLog = 'D:\farming_management_system\storage\logs\security.jsonl'
$serviceName = 'WazuhSvc'
$marker = '<location>D:\farming_management_system\storage\logs\security.jsonl</location>'

if (-not (Test-Path -LiteralPath $configPath)) {
    throw "Wazuh agent configuration was not found at $configPath"
}

$logDirectory = Split-Path -Parent $securityLog
if (-not (Test-Path -LiteralPath $logDirectory)) {
    New-Item -ItemType Directory -Path $logDirectory -Force | Out-Null
}
if (-not (Test-Path -LiteralPath $securityLog)) {
    New-Item -ItemType File -Path $securityLog -Force | Out-Null
}

$config = Get-Content -LiteralPath $configPath -Raw
if ($config.Contains($marker)) {
    Write-Host 'SmartKrishi security log is already configured in Wazuh.' -ForegroundColor Yellow
} else {
    $closingTag = '</ossec_config>'
    $lastClosingTag = $config.LastIndexOf($closingTag, [StringComparison]::OrdinalIgnoreCase)
    if ($lastClosingTag -lt 0) {
        throw "Invalid Wazuh configuration: $closingTag was not found."
    }

    $timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
    $backupPath = "$configPath.smartkrishi-$timestamp.bak"
    Copy-Item -LiteralPath $configPath -Destination $backupPath

    $localFileBlock = @"

  <!-- SmartKrishi application security events -->
  <localfile>
    <location>$securityLog</location>
    <log_format>json</log_format>
    <label key="application">smartkrishi</label>
  </localfile>

"@

    $updatedConfig = $config.Insert($lastClosingTag, $localFileBlock)
    Set-Content -LiteralPath $configPath -Value $updatedConfig -Encoding utf8
    Write-Host "Configuration updated. Backup: $backupPath" -ForegroundColor Green
}

Restart-Service -Name $serviceName
$service = Get-Service -Name $serviceName
if ($service.Status -ne 'Running') {
    throw "Wazuh service did not restart successfully. Current status: $($service.Status)"
}

Write-Host 'Wazuh service is running and SmartKrishi security-log monitoring is enabled.' -ForegroundColor Green
Write-Host "Monitored file: $securityLog"
