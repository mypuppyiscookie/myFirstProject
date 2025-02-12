<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

function loginUser($id, $password){
    global $conn;
    include_once __DIR__ . "/../db.php";

    if(!$conn){
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if(!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");

    $sql = "SELECT userId, userPassword, userName FROM users WHERE userId = '" . $id . "' ";
    $result = mysqli_query($conn, $sql); //SQL 실행 및 오류 체크
    if (!$result) {
        die("쿼리 오류: " . mysqli_error($conn)); //SQL 실행 오류 확인
    }

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['userPassword'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        
            $_SESSION["userId"] = $user["userId"];
            $_SESSION["userName"] = $user["userName"];
            $_SESSION["isLoggedIn"] = true;

            return ["userId" => $user["userId"], "userName" => $user["userName"], "isLoggedIn" => true];
        }
    }
    return false;
}

function logout() {
    if (session_status() !== PHP_SESSION_ACTIVE) { 
        session_start();
    }
    $_SESSION = [];

    // 세션 쿠키 삭제
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    
    return ["success" => true, "message" => "로그아웃 성공!"];
}

?>