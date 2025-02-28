<?php
require_once "config.php"; 

header('Content-Type: application/json');

if (!isset($_SESSION["adminId"]) || !isset($_SESSION["adminName"])) {
    echo json_encode(["success" => false, "message" => "관리자 세션 정보가 없습니다."]);
    exit;
}

echo json_encode([
    "success" => true,
    "adminId" => $_SESSION["adminId"],
    "adminName" => $_SESSION["adminName"]
], JSON_UNESCAPED_UNICODE);
?>
