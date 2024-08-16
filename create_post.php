<?php
require 'config.php';
require 'db.php';


if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}



$title = '';
$content = '';
$file_path = '';
$error_message = '';
$file_error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 제목과 내용 유효성 검사
    if (isset($_POST['title']) && isset($_POST['content'])) {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        // 제목과 내용이 비어있는지 검사
        if (empty($title) || empty($content)) {
            $error_message = "제목 또는 내용이 비어있습니다.";
        } else {
            // 파일 업로드 처리
            if (isset($_FILES['file'])) {
                if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
                    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'c', 'sql'];
                    $file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
                    $max_file_size = 5 * 1024 * 1024; // 5MB

                    if ($_FILES['file']['size'] > $max_file_size) {
                        $file_error_message = "파일 용량 초과 (최대 5MB)";
                    } elseif (!in_array($file_ext, $allowed_exts)) {
                        $file_error_message = "허용되지 않는 파일 형식입니다.";
                    } else {
                        $file_name = time() . '_' . uniqid() . '_' . basename($_FILES['file']['name']);
                        $upload_dir = __DIR__ . '/uploads/';
                        $new_file_path = $upload_dir . $file_name;

                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }

                        if (move_uploaded_file($_FILES['file']['tmp_name'], $new_file_path)) {
                            $file_path = $new_file_path;
                        } else {
                            $file_error_message = "파일 업로드 실패!";
                        }
                    }
                } elseif ($_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $file_error_message = "파일 업로드 중 오류가 발생했습니다.";
                }
            }

            if (empty($error_message) && empty($file_error_message)) {
                // 게시물 추가
                $user_id = $_SESSION['id']; // 현재 로그인한 사용자의 ID
                $stmt = $mysqli->prepare("INSERT INTO posts (title, content, file_path, user_id) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $title, $content, $file_path, $user_id);

                if ($stmt->execute()) {
                    echo "<script>alert('게시물이 작성되었습니다.'); window.location.href='index.php';</script>";
                    exit();
                } else {
                    $error_message = "게시물 작성 실패!";
                }

                $stmt->close();
            }
        }
    } else {
        $error_message = "제목 또는 내용이 누락되었거나, 파일 용량을 초과했습니다. (최대 5MB)";
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>새 글 쓰기</title>
</head>
<body>
    <a href="index.php">메인으로</a>
    <h1>새 글 쓰기</h1>
    <hr>
    <form method="POST" action="" enctype="multipart/form-data">
        <label for="title"><strong>제목:</strong></label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
        <br>
        <label for="content">내용:</label>
        <textarea id="content" name="content" required><?php echo htmlspecialchars($content); ?></textarea>
        <br>
        <label for="file">파일 업로드:</label>
        <input type="file" id="file" name="file">
        <br>
        <?php if ($error_message): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if ($file_error_message): ?>
            <p style="color: red;"><?php echo htmlspecialchars($file_error_message); ?></p>
        <?php endif; ?>
        <button type="submit">게시</button>
    </form>
</body>
</html>

