# SmartKrishi activity monitoring

Admin dashboard > Security Activity shows the latest 200 alerts in 24 hours.
This is a rules-based prototype, not machine learning or proof of fraud.

Coverage and rules:
- Existing role/ownership-denial logging hooks: five denials of the same type
  per account within ten minutes produce an alert.
- supplier/my_supplies.php: supply price changes of 50% or more, or ten price
  updates in ten minutes, produce review alerts.
- farmer/update_order_status.php: ten successful status changes in ten minutes
  produce review alerts. Business operations remain allowed.
- F_Doctor.php and F_insects.php share a per-user quota: ten attempts in ten
  minutes and fifty accepted attempts per rolling day. Invalid uploads consume
  quota. Excess attempts receive HTTP 429 before scanning or external API calls.
  Chatbot endpoints and other price/order endpoints are not covered.

storage/activity is blocked from the web and ignored by Git. Per-account file
locks serialize quota updates. Old events are pruned on the next account event;
inactive files require periodic operational cleanup. This is single-server
storage. Monitoring failures log an error but do not interrupt marketplace
transactions; AI requests fail closed with 503 if quota storage is unavailable.

Run: php security/activity_test.php
Tests cover normal/anomalous changes, limits, expiration and persisted quotas.
Temporary test records are removed. Thresholds are demonstration defaults;
evaluate false positives with realistic controlled activity before deployment.
No measured real-world detection accuracy is claimed.
