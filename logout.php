<?php
session_start();
session_unset();
session_destroy();

// 로그인 상태 유지 쿠키 제거
if (isset($_COOKIE['id']) && isset($_COOKIE['username'])) {
    setcookie('id', '', time() - 3600, "/");
    setcookie('username', '', time() - 3600, "/");
    setcookie('remember_me', '', time() - 3600, "/");
}

echo "<script>alert('로그아웃 되었습니다.'); window.location.href = 'login.php';</script>";
exit();
?>