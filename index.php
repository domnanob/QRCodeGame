<?php
if (isset($_GET['redirect'])) {
    header("Location: ./client/index.php?redirect=true");
    die;
}
header("Location: ./client/index.php");