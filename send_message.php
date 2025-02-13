<?php
session_start();
require_once "init.php";
header('Content-Type: application/json');

$thread_id = $_GET['thread_id'] ?? $_SESSION['thread_id'] ?? $_COOKIE['thread_id'] ?? null;
$user_message = $_POST['message'] ?? "Hello!";

// If no thread_id, return error
if (!$thread_id) {
    echo json_encode(["error" => "Thread ID not found. Please reload the page."]);
    exit;
}

$data = [
    "role" => "user",
    "content" => $user_message,
	
];

$ch = curl_init("https://api.openai.com/v1/threads/$thread_id/messages");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $api_key,
    "Content-Type: application/json",
	"OpenAI-Beta: assistants=v2"	
]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

$resp_json = json_decode($response);
if ($resp_json && isset($resp_json->id)){
	//message added
	//run the thread
	
	$ch = curl_init("https://api.openai.com/v1/threads/$thread_id/runs");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		"Authorization: Bearer " . $api_key,
		"Content-Type: application/json",
		"OpenAI-Beta: assistants=v2"	
	]);
	$data = [
		'assistant_id'=>$assistant_id,
		//'instructions'=>$instructions
	];
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	$response = curl_exec($ch);
	curl_close($ch);		
}

echo $response;
