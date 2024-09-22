<?php
require_once 'init.php';

// 이미 로그인 된 경우 index.php로 리다이렉트
if (isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);  // "로그인 상태 유지" 체크박스 확인

    // 필수 필드 확인
    if (empty($username) || empty($password)) {
        $errors[] = "아이디와 비밀번호를 입력해주세요.";
    } else {
        // $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE username = ?");
        // $stmt->bind_param("s", $username);
        // $stmt->execute();
        // $stmt->bind_result($id, $hashed_password);
        // $stmt->fetch();
        // $stmt->close();

        $query = "SELECT id, password FROM users WHERE username = '$username' AND password = '$password'";
        $result = $mysqli->query($query); // 사용자 입력을 직접 쿼리에 사용

        // if ($id && password_verify($password, $hashed_password)) {
        if ($result && $result->num_rows > 0) {
            // 결과에서 사용자 정보 가져오기
            $row = $result->fetch_assoc();
            $id = $row['id'];
              
            // 세션에 사용자 정보 저장
            session_regenerate_id(true); // 세션 ID 재생성
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;

            // "로그인 상태 유지" 체크박스가 선택된 경우
            if ($remember_me) {
                // 쿠키 설정 (7일 동안 유효) - 보안 플래그 추가
                $cookie_time = time() + (7 * 24 * 60 * 60);  // 7일
                setcookie('id', $id, $cookie_time, "/", "", true, true); // Secure 및 HttpOnly 플래그 설정
                setcookie('username', $username, $cookie_time, "/", "", true, true);
                setcookie('remember_me', 'true', $cookie_time, "/", "", true, true);
            }

            echo "<script>alert('로그인 성공!'); window.location.href = 'index.php';</script>";
            exit();
        } else {
            $errors[] = "아이디 또는 비밀번호가 잘못되었습니다.";
        }
    }
    $mysqli->close();
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
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
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
