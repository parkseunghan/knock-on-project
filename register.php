<?php
$mysqli = new mysqli("localhost", "root", "", "board");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$errors = [];
$username_exists = false;  // 아이디 중복 상태를 저장하는 변수

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nickname = trim($_POST['nickname']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // 필수 필드 확인
    if (empty($username) || empty($nickname) || empty($password) || empty($password_confirm)) {
        $errors[] = "모든 필드를 입력해주세요.";
    }

    // 아이디 유효성 검사 (영문, 숫자 혼합, 20자 이하, 공백 불허)
    if (!preg_match('/^[a-zA-Z0-9]{1,20}$/', $username)) {
        $errors[] = "아이디는 영문과 숫자를 혼합하여 20자 이내로 입력해야 하며, 공백을 포함할 수 없습니다.";
    }

    // 닉네임 유효성 검사 (공백 불허)
    if (!preg_match('/^\S+$/', $nickname)) {
        $errors[] = "닉네임은 공백을 포함할 수 없습니다.";
    }

    // 비밀번호 유효성 검사 (영문, 숫자, 특수문자 포함, 10자 이상, 공백 불허)
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{10,}$/', $password)) {
        $errors[] = "비밀번호는 영문, 숫자, 특수문자를 포함하여 10자 이상이어야 하며, 공백을 포함할 수 없습니다.";
    }

    // 비밀번호 확인
    if ($password !== $password_confirm) {
        $errors[] = "비밀번호와 비밀번호 확인이 일치하지 않습니다.";
    }

    // 중복 사용자 검사
    if (empty($errors) && !$username_exists) {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "이미 존재하는 아이디입니다.";
        } else {
            // 비밀번호 해시 처리
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            // 새로운 사용자 등록
            $stmt = $mysqli->prepare("INSERT INTO users (username, nickname, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $nickname, $password_hashed);

            if ($stmt->execute()) {
                echo "<script>alert('회원가입 성공!'); window.location.href = 'login.php';</script>";
                exit();
            } else {
                $errors[] = "회원가입 실패!";
            }
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
    <title>회원가입</title>
    <script>
        function checkUsername() {
            const username = document.getElementById('username').value;
            const resultDiv = document.getElementById('username_result');

            if (username.length < 1) {
                resultDiv.innerHTML = "";
                return;
            }

            fetch('check_username.php?username=' + encodeURIComponent(username))
                .then(response => response.text())
                .then(data => {
                    resultDiv.innerHTML = data;
                });
        }
    </script>
</head>
<body>
    <a href="index.php">메인으로</a>
    <hr>
    
    <h1>회원가입</h1>
    
    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="username">아이디:</label>
        <input type="text" id="username" name="username" required onblur="checkUsername()">
        <div id="username_result"></div>
        <label for="nickname">닉네임:</label>
        <input type="text" id="nickname" name="nickname" required>
        <br>
        <label for="password">비밀번호:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="password_confirm">비밀번호 확인:</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
        <br>
        <button type="submit">회원가입</button>
    </form>
</body>
</html>
