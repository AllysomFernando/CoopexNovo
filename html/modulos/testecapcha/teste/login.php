<?php
const SECRET_KEY = '0x4AAAAAAAIvyg8pyRfleu82akF5MdSCvto';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['cf-turnstile-response']; // Captcha response token
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP']; 

    $formData = array(
        'secret' => SECRET_KEY,
        'response' => $token,
        'remoteip' => $ip
    );

    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($formData),
            'ignore_errors' => true // Allow reading error responses
        )
    );

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === false) {
        echo "Error while verifying CAPTCHA.";
    } else {
        $outcome = json_decode($result, true);
        if ($outcome['success']) {
            echo "Verification succeeded!";
        } else {
            echo "Verification failed.";
        }
    }
}
?>
