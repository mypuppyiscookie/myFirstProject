<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once("../../models/main/mainModel.php");
require_once "../../config.php"; 

header("Content-Type: application/json; charset=UTF-8");

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $action = $_GET["action"] ?? "";
    //모든 데이터베이스 목록 요청
    if ($action === "getDatabases") {
        $databases = getDatabaseList();
        if (!empty($databases)) {
            $response["success"] = true;
            $response["databases"] = $databases;
        } else {
            $response["message"] = "데이터베이스 목록을 가져오지 못했습니다.";
        }
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
        $data = getTableData($database, $table);
        $primaryKey = getPrimaryKey($database, $table);
        if ($database && $table) {
            $response["data"] = getTableData($database, $table);
            $response["primaryKey"] = getPrimaryKey($database, $table);
            if (is_array($data) && count($data) > 0) {
                $response = [
                    "success" => $data ? true : false,
                    "primaryKey" => $primaryKey,
                    "data" => $data
                ];
            } else {
                $response["success"] = false;
                $response["message"] = "데이터를 가져올 수 없습니다.";
            }
        } else {
            $response["message"] = "잘못된 요청: 데이터베이스 또는 테이블이 지정되지 않았습니다.";
        }
    }
}
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
?>
