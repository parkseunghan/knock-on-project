<?php
require 'config.php';
require 'db.php';

// 로그인 여부 확인
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// 현재 사용자 ID
$user_id = $_SESSION['id'];
$error = "";
$success = "";

// 사용자 비밀번호 확인
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];

    // 현재 비밀번호 가져오기
    $stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // 비밀번호 확인
    if (password_verify($password, $hashed_password)) {
        // 비밀번호가 맞으면 회원 탈퇴
        $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // 세션 종료 및 로그아웃
        session_unset();
        session_destroy();

        echo "<script>alert('회원탈퇴가 완료되었습니다.'); window.location.href = 'index.php';</script>";
        exit();
    } else {
        $error = "비밀번호가 일치하지 않습니다.";
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원 탈퇴</title>
    <script>
        function confirmDelete() {
            return confirm("정말로 회원탈퇴를 하시겠습니까?");
        }
    </script>
</head>
<body>
    <a href="index.php">메인으로</a>
    <hr>
    <h1>회원 탈퇴</h1>
    <hr>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" action="" onsubmit="return confirmDelete();">
        <label for="password">비밀번호:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">회원 탈퇴</button>
    </form>
</body>
</html>
