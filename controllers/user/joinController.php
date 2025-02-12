<?php
require_once "../../config.php";
include_once("../../models/user/joinModel.php");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? '';

    if ($action === "checkId") {
        $id = trim($_POST["id"]);
        if (!preg_match("/^[a-zA-Z0-9]{5,13}$/", $id)) {
            $response["message"] = "아이디 형식이 올바르지 않습니다";
        } elseif (checkUserIdExists($id)) {
            $response["message"] = "이미 사용 중인 아이디입니다람쥐";
            $_SESSION["isIdChecked"] = false;
        } else {
            $response["success"] = true;
            $response["message"] = "사용 가능한 아이디입니다";
            $_SESSION["isIdChecked"] = true;
            $_SESSION["checkedId"] = $id;
        }
    } elseif ($action === "join") {
        $id = trim($_POST["id"]);
        $password = trim($_POST["password"]);
        $name = trim($_POST["name"]);
        $gender = trim($_POST["gender"]);
        $email = trim($_POST["email"]);
        $birthday = trim($_POST["birthday"]);

        if (!$id || !$password || !$name || !$gender || !$email || !$birthday) {
            $response["message"] = "모든 정보를 입력해주세요.";
        }

        if (!isset($_SESSION["isIdChecked"]) || $_SESSION["isIdChecked"] !== true || $_SESSION["checkedId"] !== $id) {
            $response["message"] = "아이디 중복을 확인해주세요.";
        } elseif (!preg_match("/^[a-zA-Z0-9]{5,13}$/", $id)) {
            $response["message"] = "아이디 형식이 올바르지 않습니다";
        } elseif (!preg_match("/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/", $password)) {
            $response["message"] = "비밀번호 형식이 올바르지 않습니다";
        } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|net)$/", $email)) {
            $response["message"] = "이메일 형식이 올바르지 않습니다";
        } elseif (!preg_match("/^\d{8}$/", $birthday)) {
            $response["message"] = "생년월일 형식이 올바르지 않습니다";
        } else {
            if (joinUser($id, $password, $name, $gender, $email, $birthday)) {
                $_SESSION["userName"] = $name; //가입한 이름 세션에 저장
                $_SESSION["userId"] = $id;
                $response["success"] = true;
                $response["redirect"] = "/views/user/join/joinSuccess.php";
                unset($_SESSION["isIdChecked"]);
                unset($_SESSION["checkedId"]);
            } else {
                $response["message"] = "회원가입에 실패했습니다. 다시 시도해주세요.";
            }
        }
    }
}

echo json_encode($response);
exit;
