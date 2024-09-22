<?php
require_once 'init.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['check_username'])) {
    $username = trim($_POST['username']);
    $nickname = trim($_POST['nickname']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($username) || empty($nickname) || empty($password) || empty($password_confirm)) {
        $errors[] = "모든 필드를 입력해주세요.";
    }

    // 아이디 유효성 검사 (영문과 숫자의 조합, 20자 이하, 공백 불허)
    if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{1,20}$/', $username)) {
        $errors[] = "아이디는 영문자와 숫자를 혼합하여 20자 이내로 입력해야 하며, 공백을 포함할 수 없습니다.";
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

    if (empty($errors)) {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "이미 존재하는 아이디입니다.";
        } else {
            // $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO users (username, nickname, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $nickname, $password);

            if ($stmt->execute()) {
                echo "<script>alert('회원가입 성공!'); window.location.href = 'login.php';</script>";
                exit();
            } else {
                $errors[] = "회원가입 실패!";
            }
        }

        $stmt->close();
    }

    $mysqli->close();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_username'])) {
    $username = trim($_POST['check_username']);
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }

    $stmt->close();
    $mysqli->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입</title>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById('username').addEventListener('input', checkUsername);
            document.getElementById('nickname').addEventListener('input', validateNickname);
            document.getElementById('password').addEventListener('input', validatePassword);
            document.getElementById('password_confirm').addEventListener('input', validatePasswordConfirm);
        });

        async function checkUsername() {
            const username = document.getElementById('username').value;
            const usernameMessage = document.getElementById('username_message');

            if (username.length < 1) {
                usernameMessage.textContent = "";
                return;
            }

            // 아이디 유효성 검사
            if (!/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{1,20}$/.test(username)) {
                usernameMessage.textContent = "아이디는 영문자와 숫자를 혼합하여 20자 이내로 입력해야 하며, 공백을 포함할 수 없습니다.";
                usernameMessage.style.color = "red";
            } else {
                try {
                    const response = await fetch('register.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `check_username=${encodeURIComponent(username)}`,
                    });
                    const data = await response.json();

                    if (data.exists) {
                        usernameMessage.textContent = "이미 존재하는 아이디입니다.";
                        usernameMessage.style.color = "red";
                    } else {
                        usernameMessage.textContent = "사용 가능한 아이디입니다.";
                        usernameMessage.style.color = "green";
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            }
        }

        function validateNickname() {
            const nickname = document.getElementById('nickname').value;
            const nicknameMessage = document.getElementById('nickname_message');

            if (/^\S+$/.test(nickname)) {
                nicknameMessage.textContent = "올바른 형식입니다.";
                nicknameMessage.style.color = "green";
            } else {
                nicknameMessage.textContent = "닉네임은 공백을 포함할 수 없습니다.";
                nicknameMessage.style.color = "red";
            }
        }

        function validatePassword() {
            const password = document.getElementById('password').value;
            const passwordMessage = document.getElementById('password_message');
            const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{10,}$/;

            if (passwordRegex.test(password)) {
                passwordMessage.textContent = "올바른 형식입니다.";
                passwordMessage.style.color = "green";
            } else {
                passwordMessage.textContent = "비밀번호는 영문, 숫자, 특수문자를 포함하여 10자 이상이어야 하며, 공백을 포함할 수 없습니다.";
                passwordMessage.style.color = "red";
            }
        }

        function validatePasswordConfirm() {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            const passwordConfirmMessage = document.getElementById('password_confirm_message');

            if (password === passwordConfirm) {
                passwordConfirmMessage.textContent = "비밀번호가 일치합니다.";
                passwordConfirmMessage.style.color = "green";
            } else {
                passwordConfirmMessage.textContent = "비밀번호와 비밀번호 확인이 일치하지 않습니다.";
                passwordConfirmMessage.style.color = "red";
            }
        }

        function validateForm() {
            checkUsername();
            validateNickname();
            validatePassword();
            validatePasswordConfirm();

            return document.querySelectorAll("div[style='color: red;']").length === 0;
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

    <form method="POST" action="" onsubmit="return validateForm()">
        <label for="username">아이디:</label>
        <input type="text" id="username" name="username" required>
        <div id="username_message"></div>
        <br>

        <label for="nickname">닉네임:</label>
        <input type="text" id="nickname" name="nickname" required>
        <div id="nickname_message"></div>
        <br>

        <label for="password">비밀번호:</label>
        <input type="password" id="password" name="password" required>
        <div id="password_message"></div>
        <br>

        <label for="password_confirm">비밀번호 확인:</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
        <div id="password_confirm_message"></div>
        <br>

        <button type="submit">회원가입</button>
    </form>
</body>
</html>
