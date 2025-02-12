<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once '../../models/main/setUpModel.php';
require_once "../../config.php";

header("Content-Type: application/json; charset=UTF-8");

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $action = $_GET["action"] ?? "";

    if ($action === "getUserInfo") {
        $userId = $_SESSION['userId'];
        $response["data"] = getUserInfo($userId);
        $response["success"] = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "updateUser") {
        $userId = $_POST['id'];
        $userName = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $birthday = $_POST['birthday'] ?? '';

        if (updateUser($userId, $userName, $email, $birthday)) {
            $response["success"] = true;
            $response["message"] = "회원 정보가 업데이트되었습니다.";
        } else {
            $response["message"] = "회원 정보 업데이트 실패.";
        }
    }

    if ($action === "changePassword") {
        $userId = $_SESSION['userId'];
        $currentPassword = $_POST['currentPassword'] ?? '';
        $newPassword = $_POST['newPassword'] ?? '';

        if (!verifyPassword($userId, $currentPassword)) {
            $response["message"] = "현재 비밀번호가 올바르지 않습니다.";
        } else {
            if (changePassword($userId, $newPassword)) {
                $response["success"] = true;
                $response["message"] = "비밀번호가 변경되었습니다.";
            } else {
                $response["message"] = "비밀번호 변경 실패.";
            }
        }
    }

    if ($action === "deleteAccount") {
        $userId = $_SESSION['userId'];
        $password = $_POST['password'] ?? '';

        if (!verifyPassword($userId, $password)) {
            $response["message"] = "비밀번호가 올바르지 않습니다.";
        } else {
            if (deleteUser($userId)) {
                session_destroy();
                $response["success"] = true;
                $response["message"] = "회원 탈퇴가 완료되었습니다.";
            } else {
                $response["message"] = "회원 탈퇴 실패.";
            }
        }
    }
}

echo json_encode($response);
