<?php
require_once __DIR__ . '/public_html/tools/config.php';

// Get the webhook URL from environment variable or use the one below
$webhookUrl = getenv('WEBHOOK_URL') ?: 'https://starsnbot.onrender.com/webhook.php';

// Set webhook
$result = file_get_contents("https://api.telegram.org/bot".API_KEY."/setWebhook?url=".urlencode($webhookUrl));

// Display result
echo "<pre>";
print_r(json_decode($result, true));
echo "</pre>";

// Check webhook info
$webhookInfo = file_get_contents("https://api.telegram.org/bot".API_KEY."/getWebhookInfo");

echo "<h2>Webhook Info:</h2>";
echo "<pre>";
print_r(json_decode($webhookInfo, true));
echo "</pre>";
?>
