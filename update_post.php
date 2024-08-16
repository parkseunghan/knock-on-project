<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$mysqli = new mysqli("localhost", "user", "user", "board");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_GET['id']) && !is_array($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    echo "잘못된 요청입니다.";
    exit();
}

// 게시물 데이터 가져오기
$stmt = $mysqli->prepare("SELECT title, content, file_path, user_id FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($title, $content, $existing_file_path, $post_user_id);

if (!$stmt->fetch()) {
    echo "게시물이 존재하지 않습니다.";
    exit();
}

$stmt->close();

// 로그인한 사용자가 게시물의 작성자인지 확인
if ($_SESSION['id'] !== $post_user_id) {
    echo "게시물 수정 권한이 없습니다.";
    exit();
}

$file_path = $existing_file_path;
$error_message = '';
$file_error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['title']) && isset($_POST['content'])) {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $upload_dir = __DIR__ . '/uploads/';

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
                    $new_file_path = $upload_dir . $file_name;

                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    if (move_uploaded_file($_FILES['file']['tmp_name'], $new_file_path)) {
                        // 기존 파일이 있으면 삭제
                        if ($existing_file_path && file_exists($existing_file_path)) {
                            unlink($existing_file_path);
                        }
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
            // 게시물 업데이트
            $stmt = $mysqli->prepare("UPDATE posts SET title = ?, content = ?, file_path = ? WHERE id = ?");
            $stmt->bind_param("sssi", $title, $content, $file_path, $id);

            if ($stmt->execute()) {
                echo "<script>alert('게시물이 수정되었습니다.'); window.location.href='index.php';</script>";
                exit();
            } else {
                $error_message = "게시물 수정 실패!";
            }

            $stmt->close();
        }
    } else {
        $error_message = "제목 또는 내용이 누락되었거나, 파일 용량을 초과했습니다. (최대 5MB)";
    }
}

$stmt = $mysqli->prepare("SELECT title, content, file_path FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시물 수정</title>
</head>
<body>
    <a href="index.php">메인으로</a>
    <h1>게시물 수정</h1>
    <hr>
    <form method="POST" action="" enctype="multipart/form-data">
        <label for="title"><strong>제목:</strong></label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        <br>
        <label for="content">내용:</label>
        <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
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
        <button type="submit">수정</button>
    </form>
</body>
</html>

