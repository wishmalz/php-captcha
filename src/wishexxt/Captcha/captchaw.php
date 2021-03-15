<?php

// Start a session to access the captcha externally
session_start();

$config = require("config.php");

// The captcha will be stored in this variable
$captcha = '';

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

for ($i = 0; $i < $totalCharacters; $i++) {
    $captcha .= substr($possibleLetters, mt_rand(0, strlen($possibleLetters) - 1), 1);
}
$_SESSION['captcha'] = $captcha;