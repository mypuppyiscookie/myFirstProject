<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 일반 유저 세션과 관리자 세션을 구분
if (!isset($_SESSION['userId']) && !isset($_SESSION['adminId'])) {
    $_SESSION['userId'] = null;
    $_SESSION['userName'] = null;
    $_SESSION['adminId'] = null;
    $_SESSION['adminName'] = null;
}
?>
