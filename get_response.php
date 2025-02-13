<?php
session_start();
require_once "init.php";

$thread_id = $_GET['thread_id'] ?? $_SESSION['thread_id'] ?? $_COOKIE['thread_id'] ?? null;

// If no thread_id, return empty response
if (!$thread_id) {
    echo json_encode(["error" => "Thread ID not found."]);
    exit;
}

$ch = curl_init("https://api.openai.com/v1/threads/$thread_id/messages");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $api_key,
	"OpenAI-Beta: assistants=v2"
]);

$response = curl_exec($ch);
curl_close($ch);

// Return full chat history
echo $response;
