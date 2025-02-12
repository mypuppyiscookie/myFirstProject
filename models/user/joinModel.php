<?php
function checkUserIdExists($id) {
    global $conn;
    include_once __DIR__ . "/../db.php";

    if(!$conn){
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if(!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");

    $sql = "SELECT userId FROM users WHERE userId = '$id'";
    $result = mysqli_query($conn, $sql);

    $rtn_val = mysqli_num_rows($result) > 0;
    mysqli_close($conn);

    return $rtn_val;
}

function joinUser($id, $password, $name, $gender, $email, $birthday) {
    global $conn;
    include_once __DIR__ . "/../db.php";

    if(!$conn){
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if(!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");

    // 비밀번호 해싱
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 회원가입 쿼리 실행
    $sql = "INSERT INTO users (userId, userPassword, userName, gender, email, birthday) 
            VALUES ('$id', '$hashedPassword', '$name', '$gender', '$email', '$birthday')";

    $result = mysqli_query($conn, $sql);
    if ($result) { 
        mysqli_close($conn); 
        return true;
    } else {
        mysqli_close($conn); 
        return false;
    }
}
?>
