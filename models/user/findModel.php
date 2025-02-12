<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../services/mailService.php';

function findIdByNameAndEmail($name, $email)
{
    global $conn;
    require_once __DIR__ . '/../db.php';
    if (!$conn) {
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if (!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");

    $query = "SELECT userId FROM users WHERE userName = '$name' AND email = '$email'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['userId'];
    }
    return null;
}

function findPwdById($id)
{
    global $conn;
    require_once __DIR__ . '/../db.php';
    if (!$conn) {
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if (!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");

    $query = "SELECT email FROM users WHERE userId = '$id'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['email'];
    }
    return null;
}

function updatePassword($id, $hashed_pw)
{
    global $conn;
    require_once __DIR__ . '/../db.php';
    if (!$conn) {
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if (!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");

    $query = "UPDATE users SET userPassword = '$hashed_pw' WHERE userId = '$id'";
    return $conn->query($query);
}
