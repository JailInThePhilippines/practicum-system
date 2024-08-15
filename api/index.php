<?php

// Function to generate a random string
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length));
}

// Generate a random secret key
$secret_key = generateRandomString();
echo $secret_key;

?>
