<?php
require_once "../../models/user/loginModel.php";
require_once "../../config.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $result = logout();

    echo json_encode($result);
    exit;
}
?>
