<?php
session_start();
require_once "init.php";
header('Content-Type: application/json');

$thread_id = $_GET['thread_id'] ?? $_SESSION['thread_id'] ?? $_COOKIE['thread_id'] ?? null;
$run_id  = $_GET['run_id'] ?? $_SESSION['run_id'] ?? $_COOKIE['run_id'] ?? null;
$user_message = $_POST['message'] ?? "Hello!";

// If no thread_id, return error
if (!$thread_id) {
    echo json_encode(["error" => "Thread ID not found. Please reload the page."]);
    exit;
}
if (!$run_id) {
    echo json_encode(["error" => "Run ID not found. Please reload the page."]);
    exit;
}


$ch = curl_init("https://api.openai.com/v1/threads/$thread_id/runs/$run_id");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $api_key,
	"OpenAI-Beta: assistants=v2"	
]);

$response = curl_exec($ch);
curl_close($ch);


echo $response;
