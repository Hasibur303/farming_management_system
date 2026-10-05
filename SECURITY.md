# SmartKrishi security controls

## Implemented application controls

- PDO with native prepared statements is the database backend. The compatibility
  wrapper keeps existing page controllers working while all remaining legacy
  database calls are migrated.
- Every POST, PUT, PATCH, and DELETE request requires a 256-bit session-bound CSRF
  token. Tokens are inserted into existing POST forms by the shared bootstrap.
- Role policies are enforced centrally for Admin, Farmer, Customer, Supplier,
  Labour, and Agrologist routes. Ownership checks remain part of record-changing
  queries, such as farmer order updates.
- Passwords use Argon2id when PHP supports it, otherwise bcrypt. Existing hashes
  are upgraded on successful login. Failed logins are tracked per hashed phone
  identifier and IP address.
- Image uploads are quarantined, checked by MIME type, extension, file size and
  image parser, assigned random names, scanned by ClamAV, and then moved to web
  storage. Executable extensions are denied by `uploads/.htaccess`.
- Security events are JSON Lines in `storage/logs/security.jsonl`, suitable for
  Wazuh JSON ingestion. Secrets and raw login identifiers are not logged.
- TOTP multi-factor authentication is mandatory for Administrators and optional
  for other roles. Secrets are encrypted with AES-256-GCM, verification is rate
  limited, and eight hashed one-use recovery codes are created during enrollment.
  QR codes are generated locally in the browser by the vendored MIT-licensed
  QRCode.js library, so the authenticator secret is not sent to an external API.

## Authenticator MFA

Generate a unique 32-byte key for each deployment and store its base64 value only
in `.env` as `MFA_ENCRYPTION_KEY`. Losing or changing this key makes existing MFA
enrollments unreadable. Never commit it. Administrator accounts are directed to
enrollment after password verification and cannot disable MFA in the application.
Other users can manage MFA from `security_settings.php`.

The second-factor pending session remains valid long enough to complete a configured
rate-limit lockout (15 minutes with the default 10-minute window). TOTP accepts only
the current 30-second interval plus one interval on either side for clock drift.
Security events include `authentication.mfa_enabled`, `mfa_succeeded`,
`mfa_failed`, `mfa_rate_limited`, `mfa_recovery_used`, and `mfa_disabled`.

## ClamAV on Windows

ClamAV 1.5.4 is installed under `D:\SecurityTools\ClamAV`. Update signatures
periodically with `freshclam.exe`; the local `.env` is configured as:

```ini
CLAMAV_PATH=D:\SecurityTools\ClamAV\clamscan.exe
CLAMAV_REQUIRED=true
```

With `CLAMAV_REQUIRED=true`, an unavailable or failed scanner blocks the upload.
Keep it `false` only for local development where ClamAV is not installed.

## ModSecurity and OWASP CRS

OWASP CRS 4.29.0 is installed under `D:\SecurityTools\OWASP-CRS` and its paths
are configured in `security/modsecurity-smartkrishi.conf`. The current XAMPP
Apache installation does not have ModSecurity loaded. Before enabling the file,
install a trusted ModSecurity v2 module built for this exact Apache 2.4.58 Win64
VS17 ABI, include the file from Apache's `httpd.conf`, run `httpd.exe -t`, and
restart Apache.

Start with `SecRuleEngine DetectionOnly`. Review
`storage/logs/modsecurity-audit.log` and tune false positives before changing it
to `SecRuleEngine On`. Enabling an incompatible binary module can stop Apache, so
the application does not automatically edit the machine-wide XAMPP configuration.

## OWASP ZAP test

Only scan the local test instance with test data. ZAP active scanning sends attack
payloads and can change application data.

ZAP 2.17.0 and Temurin Java 17 are installed under `D:\SecurityTools`. Run its
automation framework with:

```powershell
$env:JAVA_HOME='D:\SecurityTools\Java17'
$env:Path="$env:JAVA_HOME\bin;$env:Path"
Set-Location 'D:\SecurityTools\ZAP\ZAP_2.17.0'
.\zap.bat -cmd -autorun "D:\farming_management_system\security\zap-automation.yaml"
```

The generated report is `storage/reports/zap-report.html`. The unauthenticated
automation plan checks the public attack surface. Configure
separate authenticated ZAP contexts for each test account to verify horizontal
and vertical authorization boundaries. Never store passwords in this repository.

The latest 2026-10-03 unauthenticated scan completed successfully across 84
discovered URLs after the server remediations and MFA implementation. It reported
0 High, 4 Medium, 3 Low, and 6 Informational alert categories.
No confirmed SQL injection or XSS alert was reported. The root `.htaccess` added
after that scan disables directory indexes, blocks `.git`, `.env`, `security`,
and `storage` web access, applies security headers to static files, and removes
the PHP disclosure header. CSP warnings about `unsafe-inline`, broad HTTPS
sources, and third-party Subresource Integrity require a later front-end refactor
because removing them immediately would break the current inline scripts/styles.

## Wazuh ingestion

Copy the `<localfile>` entries from `security/wazuh-localfile.xml` into the Wazuh
agent configuration on the web server and restart the agent. Alert on events such
as `authentication.login_failed`, `authentication.rate_limited`,
`csrf.violation`, `authorization.role_denied`, `authorization.ownership_denied`,
`upload.type_rejected`, and `upload.malware_detected`.
