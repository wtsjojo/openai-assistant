<?php
session_start();
require_once "init.php";
header('Content-Type: application/json');

$query_thread_id = $_GET['thread_id'] ?? null; // Get from query string
$stored_thread_id = $_SESSION['thread_id'] ?? $_COOKIE['thread_id'] ?? null;

// Override thread_id if provided in query string
if ($query_thread_id) {
    $thread_id = $query_thread_id;
} else {
    $thread_id = $stored_thread_id;
}

if ($thread_id){
	//validate threadid
	$ch = curl_init("https://api.openai.com/v1/threads/$thread_id");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		"Authorization: Bearer " . $api_key,
		"Content-Type: application/json",
		"OpenAI-Beta: assistants=v2"
	]);
	
	curl_setopt($ch, CURLOPT_POST, 1);

    $response = curl_exec($ch);
    curl_close($ch);
	
	$thread_data = json_decode($response, true);
    $thread_id = $thread_data['id'] ?? null;

}

// If no valid thread_id exists, create a new one
if (!$thread_id) {
    $ch = curl_init("https://api.openai.com/v1/threads");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $api_key,
        "Content-Type: application/json",
		"OpenAI-Beta: assistants=v2"
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);

    $response = curl_exec($ch);
    curl_close($ch);

    $thread_data = json_decode($response, true);
    $thread_id = $thread_data['id'] ?? null;
}

// Store in session and cookie
$_SESSION['thread_id'] = $thread_id;
setcookie('thread_id', $thread_id, time() + (86400 * 30), "/"); // 30 days expiration

// Return thread ID as JSON
echo json_encode(["thread_id" => $thread_id]);
