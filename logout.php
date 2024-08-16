<?php
require 'config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 세션과 쿠키 삭제
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();

    // 쿠키 삭제
    setcookie('id', '', time() - 3600, '/');
    setcookie('username', '', time() - 3600, '/');
    setcookie('remember_me', '', time() - 3600, '/');

    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그아웃</title>
    <script>
        window.onload = function() {
            if (confirm("정말로 로그아웃하시겠습니까?")) {
                fetch('logout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(response => {
                    if (response.ok) {
                        window.location.href = 'login.php';
                    }
                }).catch(error => {
                    console.error('로그아웃 요청 실패:', error);
                });
            } else {
                window.location.href = 'index.php'; // 사용자가 취소했을 때
            }
        };
    </script>
</head>
<body>
    <!-- 페이지 내용은 필요 없음, 스크립트가 자동으로 동작합니다. -->
</body>
</html>
