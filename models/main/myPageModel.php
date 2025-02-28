<?php

function getUserInfo($userId)
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

    $query = "SELECT userId, userName, email, birthday, joinDate FROM users WHERE userId = '$userId'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("쿼리오류: " . mysqli_error($conn));
    }

    return mysqli_fetch_assoc($result);
}

function updateUser($userId, $userName, $email, $birthday)
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

    $query = "UPDATE users SET userName='$userName', email='$email', birthday='$birthday' WHERE userId= '$userId'";
    $result = mysqli_query($conn, $query);
    return $result;
}

function verifyPassword($userId, $currentPassword)
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

    $query = "SELECT userPassword FROM users WHERE userId= '$userId'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    return $user && password_verify($currentPassword, $user['userPassword']);
}

function changePassword($userId, $newPassword)
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

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $query = "UPDATE users SET userPassword='$hashedPassword' WHERE userId= '$userId'";
    return mysqli_query($conn, $query);
}

function deleteUser($userId)
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

    $query = "DELETE FROM users WHERE userId= '$userId'";
    return mysqli_query($conn, $query);
}
