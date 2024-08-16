<?php
session_start();
$mysqli = new mysqli("localhost", "user", "user", "board");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// 로그인 여부 확인
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// 현재 사용자 ID
$id = $_SESSION['id'];

$errors = [];
$success = "";

// 사용자 정보 가져오기
$stmt = $mysqli->prepare("SELECT username, nickname FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($username, $nickname);
$stmt->fetch();
$stmt->close();

// 회원정보 수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_nickname = trim($_POST['nickname']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // 필드 검증
    if (empty($new_nickname)) {
        $errors[] = "닉네임을 입력해주세요.";
    }

    if (!empty($new_password) || !empty($confirm_password)) {
        if ($new_password !== $confirm_password) {
            $errors[] = "새 비밀번호와 비밀번호 확인이 일치하지 않습니다.";
        } elseif (strlen($new_password) < 10 || !preg_match('/[A-Za-z]/', $new_password) || !preg_match('/\d/', $new_password) || !preg_match('/\W/', $new_password)) {
            $errors[] = "비밀번호는 최소 10자이며, 영문, 숫자, 특수문자를 포함해야 합니다.";
        }
    }

    // 현재 비밀번호 확인
    $stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($current_password, $hashed_password)) {
        $errors[] = "현재 비밀번호가 일치하지 않습니다.";
    }

    // 업데이트 실행
    if (empty($errors)) {
        if (!empty($new_password)) {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("UPDATE users SET nickname = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_nickname, $hashed_new_password, $id);
        } else {
            $stmt = $mysqli->prepare("UPDATE users SET nickname = ? WHERE id = ?");
            $stmt->bind_param("si", $new_nickname, $id);
        }
        if ($stmt->execute()) {
            $success = "회원정보가 성공적으로 수정되었습니다.";
        } else {
            $errors[] = "회원정보 수정 실패.";
        }
        $stmt->close();
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원정보 수정</title>
</head>
<body>
    <a href="index.php">메인으로</a>
    <hr>
    <h1>회원정보 수정</h1>
    <hr>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($success): ?>
        <p><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <p>아이디: <?php echo htmlspecialchars($username); ?></p>
        <label for="nickname">닉네임:</label>
        <input type="text" id="nickname" name="nickname" value="<?php echo htmlspecialchars($nickname); ?>" required>
        <br>
        <label for="current_password">현재 비밀번호:</label>
        <input type="password" id="current_password" name="current_password" required>
        <br>
        <label for="new_password">새 비밀번호 (변경할 경우):</label>
        <input type="password" id="new_password" name="new_password">
        <br>
        <label for="confirm_password">새 비밀번호 확인:</label>
        <input type="password" id="confirm_password" name="confirm_password">
        <br>
        <button type="submit">수정</button>
    </form>

    <a href="delete_account.php">회원탈퇴</a>
</body>
</html>
