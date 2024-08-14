<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);  // "로그인 상태 유지" 체크박스 확인

    // 필수 필드 확인
    if (empty($username) || empty($password)) {
        $errors[] = "아이디와 비밀번호를 입력해주세요.";
    } else {
        // 사용자 인증
        $mysqli = new mysqli("localhost", "root", "", "board");

        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        $stmt->close();
        $mysqli->close();

        if ($id && password_verify($password, $hashed_password)) {
            // 세션에 사용자 정보 저장
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;

            // "로그인 상태 유지" 체크박스가 선택된 경우
            if ($remember_me) {
                // 쿠키 설정 (7일 동안 유효)
                $cookie_time = time() + (7 * 24 * 60 * 60);  // 7일
                setcookie('id', $id, $cookie_time, "/");
                setcookie('username', $username, $cookie_time, "/");
                setcookie('remember_me', 'true', $cookie_time, "/");
            }

            echo "<script>alert('로그인 성공!'); window.location.href = 'index.php';</script>";
            exit();
        } else {
            $errors[] = "아이디 또는 비밀번호가 잘못되었습니다.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
</head>
<body>
    <h1>로그인</h1>
    
    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="username">아이디:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">비밀번호:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="remember_me">
            <input type="checkbox" id="remember_me" name="remember_me"> 로그인 상태 유지
        </label>
        <br>
        <button type="submit">로그인</button>
    </form>
    <a href="register.php">회원가입</a>
</body>
</html>
