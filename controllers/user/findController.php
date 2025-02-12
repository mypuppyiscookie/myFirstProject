<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../../models/user/findModel.php';
require_once __DIR__ . '/../../services/mailService.php';
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? '';

    if ($action === "findId") {
        $name = trim($_POST["name"] ?? '');
        $email = trim($_POST["email"] ?? '');

        if (!empty($name) && !empty($email)) {
            $id = findIdByNameAndEmail($name, $email);
            if ($id) {
                $response["success"] = true;
                $response["message"] = "아이디는 $id 입니다";
            } else {
                $response["message"] = "일치하는 회원 정보가 없습니다";
            }
        } else {
            $response["message"] = "이름과 이메일을 입력하세요";
        } 
    } elseif ($action === 'findPwd') {
        $id = trim($_POST['id'] ?? '');

        if (!empty($id)) {
            $email = findPwdById($id);
            if ($email) {
                $temp_pw = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
                $hashed_pw = password_hash($temp_pw, PASSWORD_DEFAULT);
                
                if (updatePassword($id, $hashed_pw)) {
                    if (sendPasswordResetEmail($email, $temp_pw)) {
                        $response["success"] = true;
                        $response["message"] = "임시 비밀번호가 이메일로 전송되었습니다";
                    } else {
                        $response["message"] = "이메일 전송 실패"; 
                    }
                } else {
                    $response["message"] = "비밀번호 업데이트 실패";
                }
            } else {
                $response["message"] = "존재하지 않는 아이디입니다";
            }
        } else {
            $response["message"] = "아이디를 입력하세요.";
        }
    } else {
        $response["message"] = "잘못된 요청입니다.";
    }
}

echo json_encode($response);  // ✅ JSON 응답 출력 추가
?>
