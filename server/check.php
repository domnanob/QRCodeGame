<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $req = json_decode(file_get_contents('php://input'), true);

    if (isset($req["code"])) {

        if ($req["code"] == hash("sha256", "qr_1")) //12716218d0b2862b72e1e8e41f2ac6726f45ff5f7fd081424266346482c9b013
        {
            setcookie(hash("sha256", "qr_1" . $_COOKIE[hash("sha256", "token")]), true, time() + 60 * 60 * 24 * 365, "/");
        } else if ($req["code"] == hash("sha256", "qr_2")) //ab09a8c1a56ccc324a5475bb7989a77b22dec011b65e86d3d5ad1277724a4901
        {
            setcookie(hash("sha256", "qr_2" . $_COOKIE[hash("sha256", "token")]), true, time() + 60 * 60 * 24 * 365, "/");
        } else if ($req["code"] == hash("sha256", "qr_3")) //a99f952cdd610b68f42189ce4abbd5a2bec03e0859cb3fce771a34bb1498aacc
        {
            setcookie(hash("sha256", "qr_3" . $_COOKIE[hash("sha256", "token")]), true, time() + 60 * 60 * 24 * 365, "/");
        } else if ($req["code"] == hash("sha256", "qr_4"))  //f04a032fb1da28d748974506b1e2077e0743722aaa8ae4e0a01e9266d18536e9
        {
            setcookie(hash("sha256", "qr_4" . $_COOKIE[hash("sha256", "token")]), true, time() + 60 * 60 * 24 * 365, "/");
        } else if ($req["code"] == hash("sha256", "qr_5")) //64fffd6718a85875e5647326bbc5d95e384d13bdb3450e67670e7cde4003a1de
        {
            setcookie(hash("sha256", "qr_5" . $_COOKIE[hash("sha256", "token")]), true, time() + 60 * 60 * 24 * 365, "/");
        } else if ($req["code"] == hash("sha256", "qr_6")) //ba0b483a31625b0df902d1d6c0c9163738c9082c9910511c1c0572afa2ca7e07
        {
            setcookie(hash("sha256", "qr_6" . $_COOKIE[hash("sha256", "token")]), true, time() + 60 * 60 * 24 * 365, "/");
        } else if ($req["code"] == hash("sha256", "qr_7")) //9ca740f5f7155817d4d9f0f3a29e84fa53ee82aeebd41e6289a2d0b6555c5248
        {
            setcookie(hash("sha256", "qr_7" . $_COOKIE[hash("sha256", "token")]), true, time() + 60 * 60 * 24 * 365, "/");
        } else if ($req["code"] == hash("sha256", "qr_8")) //40e11eeb55836f45877356f83b2962c880ece80ff4e6b5dd95c1a591dfe50780
        {
            setcookie(hash("sha256", "qr_8" . $_COOKIE[hash("sha256", "token")]), true, time() + 60 * 60 * 24 * 365, "/");
        }
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false]);
    }

} else {
    header("Location: ../?redirect=true");
    die();
}
