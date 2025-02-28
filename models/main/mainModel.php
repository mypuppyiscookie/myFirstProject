<?php
function getDatabaseList()
{
    global $conn;
    include_once __DIR__ . "/../db.php";
    if (!$conn) {
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if (!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");

    $databases = [];
    $sql = "SHOW DATABASES";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return [];
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $databases[] = $row["Database"] ?? $row["database"];
    }
    return $databases;
}

function getTablesInDatabase($database)
{
    global $conn;
    include_once __DIR__ . "/../db.php";
    if (!$conn) {
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if (!$conn) {
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

function getTableData($database, $table) //테이블의 데이터 불러오기
{
    global $conn;
    include_once __DIR__ . "/../db.php";
    if (!$conn) {
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if (!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");

    $data = [];
    mysqli_select_db($conn, $database);
    $sql = "SELECT * FROM `$table`";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die(json_encode([
            "success" => false,
            "message" => "sql오류" . mysqli_error($conn)
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

function getPrimaryKey($database, $table) { //기본키 칼럼명 불러오기기
    global $conn;

    if (!$conn) {
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if (!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");

    mysqli_select_db($conn, $database);
    $query = "SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        return false;
    }

    $row = mysqli_fetch_assoc($result);
    $primaryKey =  $row ? $row["Column_name"] : false;
    return $primaryKey;
}

?>
