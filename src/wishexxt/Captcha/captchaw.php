<?php

// Start a session to access the captcha externally
session_start();

$config = require("config.php");

// The captcha will be stored in this variable
$captcha = '';

// Configuration loading
$captchaHeight = $config['height'];
$captchaWidth = $config['width'];
$totalCharacters = $config['length'];
$possibleLetters = $config['symbols'];
$captchaFont = $config['font'];
$randomDots = 50;
$randomLines = 25;
$textColor = $config['textColor'];
$noiseColor = $config['noiseColor'];
$arrowColor = $config['arrowColor'];
$bgColor = $config['bgColor'];

// Generate captcha text
for ($i = 0; $i < $totalCharacters; $i++) {
    $captcha .= substr($possibleLetters, mt_rand(0, strlen($possibleLetters) - 1), 1);
}
// Store captcha for the session
$_SESSION['captcha'] = $captcha;

// Font size
$captchaFontSize = $captchaHeight * 0.65;

// Initial image
$captchaImage = imagecreatetruecolor($captchaWidth, $captchaHeight);

// Set background color
$bgColor = hexToRgb($bgColor);
$backgroundColor = imagecolorallocate($captchaImage, $bgColor['red'], $bgColor['green'], $bgColor['blue']);

// Give the image background color
imagefill($captchaImage, 0, 0, $backgroundColor);

// The PHP-file will be rendered as image
header('Content-type: image/png');

// Output the captcha as PNG image the browser
imagepng($captchaImage);

// Free memory
imagedestroy($captchaImage);

function hexToRgb($hexString)
{
    $hexDec = hexdec($hexString);

    return array(
        "red" => 0xFF & ($hexDec >> 0x10),
        "green" => 0xFF & ($hexDec >> 0x8),
        "blue" => 0xFF & $hexDec
    );
}