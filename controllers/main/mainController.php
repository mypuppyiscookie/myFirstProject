<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once("../../models/main/mainModel.php");
require_once "../../config.php"; 

header("Content-Type: application/json; charset=UTF-8");

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $action = $_GET["action"] ?? "";

    if ($action === "getDatabases") {
        $databases = getDatabaseList();
        if (!empty($databases)) {
            $response["success"] = true;
            $response["databases"] = $databases;
        } else {
            $response["message"] = "데이터베이스 목록을 가져오지 못했습니다.";
        }
    }
    // 모든 데이터베이스 목록 요청
    if ($action === "getDatabases") {
        $response["success"] = true;
        $response["databases"] = getDatabaseList();
    }
    // 특정 데이터베이스의 테이블 목록 요청
    elseif ($action === "getTables") {
        $database = $_GET["database"] ?? "";
        if ($database) {
            $tables = getTablesInDatabase($database);
            if (!empty($tables)) {
                $response["success"] = true;
                $response["tables"] = $tables;
            } else {
                $response["message"] = "테이블 목록을 가져오지 못했습니다.";
            }
        }
    }
    // 특정 테이블의 데이터 요청
    elseif ($action === "getTableData") {
        $database = $_GET["database"] ?? "";
        $table = $_GET["table"] ?? "";
        if ($database && $table) {
            $response["success"] = true;
            $response["data"] = getTableData($database, $table);
        }
    }
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
?>
