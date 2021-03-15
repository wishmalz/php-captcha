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
$direction = mt_rand(1, 2); // captcha direction
// Store captcha for the session
$direction === 1 ? $_SESSION['captcha'] = $captcha : $_SESSION['captcha'] = strrev($captcha);

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

// Bolder line for arrows
imagesetthickness($captchaImage, 4);

// Font size
$captchaLen = strlen($captcha);
$captchaFontSize = $captchaWidth / ($captchaLen * 2);

// Place text on the image
$x = $captchaFontSize;
$y = $captchaHeight / 1.75;
for ($i = 1; $i <= $captchaLen; $i++) {
    imagettftext(
        $captchaImage,
        $captchaFontSize,
        mt_rand(-30, 30),
        $x + $captchaFontSize * ($i - 1) * 2,
        $y,
        $textColor,
        $captchaFont,
        $captcha[$i - 1]
    );
    if ($i !== $captchaLen) {
        if ($direction === 1) { // straight
            arrow($captchaImage, $x + $captchaFontSize * ($i - 1) * 2 + $captchaFontSize, $y / 1.15, $x + $captchaFontSize *
                ($i - 1) * 2 + $captchaFontSize * 2, $y / 1.15, $arrowColor);
        } else { // forward
            arrow($captchaImage, $x + $captchaFontSize *
                ($i - 1) * 2 + $captchaFontSize * 2, $y / 1.15, $x + $captchaFontSize * ($i - 1) * 2 + $captchaFontSize, $y / 1.15, $arrowColor);
        }
    }

}

// The PHP-file will be rendered as png image
header('Content-type: image/png');

// Output the captcha as PNG image to the browser
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

function arrow($im, $x1, $y1, $x2, $y2, $color, $arrowLength = 10, $arrowWidth = 5)
{
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