<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require __DIR__ . '/services/mailService.php';  // ✅ mailService.php 경로 확인 필요

$testEmail = "testdragonmail1@navercom";  // ✅ 실제 이메일 주소로 변경
$tempPw = "testdragon1234";  // ✅ 테스트용 비밀번호

if (sendPasswordResetEmail($testEmail, $tempPw)) {
    echo "✅ 이메일 전송 성공! 임시 비밀번호가 전송되었습니다.";
} else {
    echo "❌ 이메일 전송 실패! SMTP 설정 확인 필요.";
}
?>
