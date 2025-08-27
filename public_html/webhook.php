<?php
// Include config file
require_once __DIR__ . '/tools/config.php';

// Get the POST request body
$update = json_decode(file_get_contents('php://input'), true);

// Process the update
if (isset($update['message'])) {
    $message = $update['message'];
    $chat_id = $message['chat']['id'];
    $text = $message['text'] ?? '';
    $message_id = $message['message_id'];
    $first_name = $message['from']['first_name'] ?? '';
    $username = $message['from']['username'] ?? '';
    
    // Your existing bot logic here
    if ($text === '/start') {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "Assalomu alaykum! Bot ishga tushdi.",
            'parse_mode' => 'HTML'
        ]);
    }
    // Add more command handlers as needed
}

// Handle callback queries
if (isset($update['callback_query'])) {
    $callback = $update['callback_query'];
    $data = $callback['data'];
    $chat_id = $callback['message']['chat']['id'];
    $message_id = $callback['message']['message_id'];
    
    // Handle callback data
    // Add your callback handlers here
    
    // Answer callback query
    bot('answerCallbackQuery', [
        'callback_query_id' => $callback['id']
    ]);
}
?>
