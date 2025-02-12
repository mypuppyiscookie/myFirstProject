<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

function getDatabaseList() {
    global $conn;
    include_once __DIR__ . "/../db.php";
    if(!$conn){
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if(!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");

    $databases = [];
    $sql = "SHOW DATABASES";
    $result = mysqli_query($conn, $sql);

    if (!$result) { // ✅ SQL 실행 실패 확인
        return [];
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $databases[] = $row["Database"] ?? $row["database"];
    }
    return $databases;
}

function getTablesInDatabase($database) {
    global $conn;
    include_once __DIR__ . "/../db.php";
    if(!$conn){
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if(!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");

    $tables = [];
    mysqli_select_db($conn, $database); 
    $sql = "SHOW TABLES";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_array($result)) {
        $tables[] = $row[0];
    }
    return $tables;
}

function getTableData($database, $table) {
    global $conn;
    include_once __DIR__ . "/../db.php";
    if(!$conn){
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if(!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");

    mysqli_select_db($conn, $database); 
    $data = [];
    $sql = "SELECT * FROM $table";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}
?>


