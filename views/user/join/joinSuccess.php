<?php
session_start();
$joinedName = $_SESSION["userName"];
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>dkbsuccess</title>
    <link rel="stylesheet" href=" /css/style.css">
</head>

<body>
    <div class="container">
        <div class="logo">회원가입 성공</div>
        <?= htmlspecialchars($joinedName) ?>님, <br>
        <p>가입해주셔서 감사합니다</p>
        <a href="/views/user/login.php" class="goto">로그인하러가기</a>
    </div>
</body>
</html>