<?php
require_once "config.php"; 

header('Content-Type: application/json');

if (!isset($_SESSION["userId"]) || !isset($_SESSION["userName"])) {
    echo json_encode(["success" => false, "message" => "세션에 유저 정보가 없습니다."]);
    exit;
}

echo json_encode([
    "success" => true,
    "userId" => $_SESSION["userId"],
    "userName" => $_SESSION["userName"]
], JSON_UNESCAPED_UNICODE);
?>
