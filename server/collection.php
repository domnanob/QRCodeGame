<?php
/**
 * collection.php
 * Read-only helper for the client's collection page. It does NOT change
 * any existing logic — it just re-reads the same per-user cookies that
 * check.php already sets and getpoints.php already reads
 * (hash("sha256", "qr_N" . token)), one trophy at a time, so the client
 * knows which stickers to show in color vs. grayscale.
 *
 * Trophy list: bump TROPHY_COUNT here once the server side is extended
 * with more qr_N branches in check.php / getpoints.php.
 */

const TROPHY_COUNT = 8;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $token = $_COOKIE[hash("sha256", "token")] ?? "";

    $collected = [];
    for ($i = 1; $i <= TROPHY_COUNT; $i++) {
        $key = "qr_$i";
        $collected[$key] = isset($_COOKIE[hash("sha256", $key . $token)]);
    }

    echo json_encode([
        "success"   => true,
        "collected" => $collected,
        "points"    => array_sum($collected),
        "total"     => TROPHY_COUNT,
    ]);
}
