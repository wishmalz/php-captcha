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
$needNoise = $config['noise'];
$randomDots = $config['noiseDotsCount'];
$randomLines = $config['noiseLinesCount'];
$textColor = $config['textColor'];
$arrowColor = $config['arrowColor'];
$bgColor = $config['bgColor'];

// Generate captcha text
for ($i = 0; $i < $totalCharacters; $i++) {
    $captcha .= substr($possibleLetters, mt_rand(0, strlen($possibleLetters) - 1), 1);
}
// Store captcha for the session
$_SESSION['captcha'] = $captcha;

// Font size
$captchaFontSize = $captchaHeight * 0.3;

// Initial image
$captchaImage = imagecreatetruecolor($captchaWidth, $captchaHeight);

// Set colors
$bgColor = setColor($captchaImage, $bgColor);
$textColor = setColor($captchaImage, $textColor);
$arrowColor = setColor($captchaImage, $arrowColor);

// Give the image background color
imagefill($captchaImage, 0, 0, $bgColor);

// If we need noise on the image
if ($needNoise) {
    // Dots noise
    for ($captchaDotsCount = 0; $captchaDotsCount < $randomDots; $captchaDotsCount++) {
        $noiseColor = imagecolorallocate($captchaImage, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
        imagefilledellipse(
            $captchaImage,
            mt_rand(0, $captchaWidth),
            mt_rand(0, $captchaHeight),
            5,
            5,
            $noiseColor
        );
    }
    // Lines noise
    for ($captchaLinesCount = 0; $captchaLinesCount < $randomLines; $captchaLinesCount++) {
        $noiseColor = imagecolorallocate($captchaImage, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
        imageline(
            $captchaImage,
            mt_rand(0, $captchaWidth),
            mt_rand(0, $captchaHeight),
            mt_rand(0, $captchaWidth),
            mt_rand(0, $captchaHeight),
            $noiseColor
        );
    }
}

arrow($captchaImage, 0, 0, 100, 100,  $arrowColor);

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

function setColor($image, $hexColor)
{
    $color = hexToRgb($hexColor);

    return imagecolorallocate($image, $color['red'], $color['green'], $color['blue']);
}

function arrow($im, $x1, $y1, $x2, $y2, $color, $arrowLength = 25, $arrowWidth = 10) {
    $distance = sqrt((($x1 - $x2) ** 2) + (($y1 - $y2) ** 2));

    $dx = $x2 + ($x1 - $x2) * $arrowLength / $distance;
    $dy = $y2 + ($y1 - $y2) * $arrowLength / $distance;

    $k = $arrowWidth / $arrowLength;

    $x2o = $x2 - $dx;
    $y2o = $dy - $y2;

    $x3 = $y2o * $k + $dx;
    $y3 = $x2o * $k + $dy;

    $x4 = $dx - $y2o * $k;
    $y4 = $dy - $x2o * $k;

    imageline($im, $x1, $y1, $dx, $dy, $color);
    imagefilledpolygon($im, array($x2, $y2, $x3, $y3, $x4, $y4), 3, $color);
}