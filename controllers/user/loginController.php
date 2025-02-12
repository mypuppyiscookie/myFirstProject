<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once "../../config.php";
include_once("../../models/user/loginModel.php");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = trim($_POST["id"]);
    $password = trim($_POST["password"]);

    $user = loginUser($id, $password); //유저 정보 받기

    if ($user) { //로그인 성공 시 유저 정보를 세션에 저장
        $_SESSION["userId"] = $user["userId"];
        $_SESSION["userName"] = $user["userName"];
        $_SESSION["isLoggedIn"] = true;
        $response["success"] = true;
        $response["redirect"] = "/views/main/main.php";
    } else {
        $response["success"] = false;
        $response["message"] = "아이디 또는 비밀번호가 존재하지 않습니다람쥐";
    }
}
// ✅ JSON 변환 및 출력
$jsonResponse = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if ($jsonResponse === false) {
    die("JSON 변환 오류: " . json_last_error_msg()); // JSON 변환 오류 확인
}

ob_end_clean(); // 출력 버퍼 정리 후 JSON만 출력
echo $jsonResponse;
