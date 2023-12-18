<?php
// Debug function to log messages
function debugLog($message) {
    $logFile = 'debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Log the form data
debugLog("Form Data: " . print_r($_POST, true));

// Process the form data and make the API request

// Retrieve the API Key ID, Secret, and relativePath from the form data
$apiKeyID = $_POST['apiKeyID'];
$secret = $_POST['secret'];
$relativePath = $_POST['relativePath']; // Use the selected option value

// Set up the API request parameters
$httpVerb = "GET";
$contentType = "application/json";
$dateValue = gmdate('D, d M Y H:i:s') . " +0000";
$stringToSign = $dateValue . "\n" . $relativePath . "\n" . $contentType . "\n" . $httpVerb . "\n";

// Calculate the signature using CryptoJS
$signature = base64_encode(hash_hmac('sha1', $stringToSign, $secret, true));

// Construct the authorization header
$authorizationHeader = "MPA " . $apiKeyID . ":" . $signature;

// Make the API request
$apiUrl = "https://edge-delivery-api.lumen.com" . $relativePath;
debugLog("API Request URL: " . $apiUrl); // Debug log for the API request URL

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: $authorizationHeader",
    "Accept: application/json",
    "Content-Type: application/json",
    "Date: $dateValue"
]);
$apiResponse = curl_exec($ch);

// Log the API response
debugLog("API Response: " . print_r($apiResponse, true));

// Return the API response as JSON
header('Content-Type: application/json');
echo $apiResponse;
?>