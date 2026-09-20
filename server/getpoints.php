<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $points = 0;
    if (isset($_COOKIE[hash("sha256", "qr_1".$_COOKIE[hash("sha256", "token")])])) {
        $points++;
    }
    if (isset($_COOKIE[hash("sha256", "qr_2".$_COOKIE[hash("sha256", "token")])])) {
        $points++;
    }
    if (isset($_COOKIE[hash("sha256", "qr_3".$_COOKIE[hash("sha256", "token")])])) {
        $points++;
    }
    if (isset($_COOKIE[hash("sha256", "qr_4".$_COOKIE[hash("sha256", "token")])])) {
        $points++;
    }
    if (isset($_COOKIE[hash("sha256", "qr_5".$_COOKIE[hash("sha256", "token")])])) {
        $points++;
    }
    if (isset($_COOKIE[hash("sha256", "qr_6".$_COOKIE[hash("sha256", "token")])])) {
        $points++;
    }
    if (isset($_COOKIE[hash("sha256", "qr_7".$_COOKIE[hash("sha256", "token")])])) {
        $points++;
    }
    if (isset($_COOKIE[hash("sha256", "qr_8".$_COOKIE[hash("sha256", "token")])])) {
        $points++;
    }
    echo json_encode(["points" => $points]);
}