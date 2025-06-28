<?php
header('Content-Type: application/json');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['reply' => 'দুঃখিত, আপনাকে লগইন করতে হবে।']);
    exit();
}

// Get the user's message
$userMessage = isset($_POST['message']) ? trim($_POST['message']) : '';

if (empty($userMessage)) {
    echo json_encode(['reply' => 'আপনি কোনো বার্তা পাঠাননি।']);
    exit();
}

// Function to call DeepSeek AI API
function getDeepSeekResponse($message) {
    $apiKey = 'sk-fbe883d13f304fa385a76ffaf12407ef'; // এখানে আপনার DeepSeek API key দিন

    $url = 'https://api.deepseek.com/v1/chat/completions';

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ];

    $data = [
        'model' => 'deepseek-chat',
        'messages' => [
            [
                'role' => 'system',
                'content' => "You are SmartKirshi AI, an expert agricultural assistant for Bangladeshi farmers.
                Provide accurate, practical farming advice in Bangla or English.
                Be concise and helpful. Always consider local Bangladeshi context.
                Current date: " . date('Y-m-d') . "."
            ],
            [
                'role' => 'user',
                'content' => $message
            ]
        ],
        'temperature' => 0.7,
        'max_tokens' => 500
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        error_log("DeepSeek API Error: HTTP $httpCode - $response");
        return false;
    }

    $result = json_decode($response, true);
    return $result['choices'][0]['message']['content'] ?? false;
}

// Get the AI response
$aiResponse = getDeepSeekResponse($userMessage);

if ($aiResponse === false) {
    // Fallback responses if API fails
    $fallbackResponses = [
        "আমি এখন আপনার প্রশ্নের উত্তর দিতে পারছি না। অনুগ্রহপূর্বক কিছুক্ষণ পর আবার চেষ্টা করুন।",
        "সার্ভারে সাময়িক সমস্যা হচ্ছে। দয়া করে পরে আবার চেষ্টা করুন।",
        "I'm having trouble connecting to the AI service. Please try again later."
    ];
    $aiResponse = $fallbackResponses[array_rand($fallbackResponses)];
}

// Return the response as JSON
echo json_encode(['reply' => $aiResponse]);
?>