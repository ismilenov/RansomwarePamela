<?php
// Allow from any origin
header("Access-Control-Allow-Origin: *"); 

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No Content
    exit();
}

// Simulated database of payment references and corresponding decryption keys
$payment_references = [
    'abc123' => '0102030405060708090a0b0c0d0e0f10',   
    'ref123' => '67890',  
];

// Initialize response
$response = [
    'status' => 'error',
    'message' => 'Invalid request',
    'decryption_key' => ''
];

// Check if a POST request is made and it contains the payment reference
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['paymentReference'])) {
        $payment_reference = $_POST['paymentReference'];

        // Check if the payment reference exists in the simulated database
        if (isset($payment_references[$payment_reference])) {
            $response['status'] = 'success';
            $response['message'] = 'Payment verified!';
            $response['decryption_key'] = $payment_references[$payment_reference];
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Invalid payment reference!';
        }
    } elseif (isset($_POST['decryptionKey'])) {
        $decryption_key = $_POST['decryptionKey'];
        
        if ($decryption_key == '12345') {  
            $response['status'] = 'success';
            $response['message'] = 'Decryption successful!';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Incorrect decryption key!';
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'No data received.';
}

// Send the response back as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
