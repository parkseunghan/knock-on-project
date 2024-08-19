<?php
require_once 'init.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$error_message = '';
$file_error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['title']) && !empty($_POST['content'])) {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        // 파일 업로드 처리
        $file_result = handleFileUpload(null); // 새 게시물이라 기존 파일 없음
        if (is_array($file_result)) {
            $file_path = $file_result['file_path'];
            $file_error_message = $file_result['error_message'];
        }

        if (empty($file_error_message)) {
            // 게시물 생성
            if (createPost($title, $content, $file_path)) {
                echo "<script>alert('게시물이 생성되었습니다.'); window.location.href='index.php';</script>";
                exit();
            } else {
                $error_message = "게시물 생성 실패!";
            }
        }
    } else {
        $error_message = "제목과 내용을 입력해야 합니다.";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시물 작성</title>
</head>
<body>
    <a href="index.php">메인으로</a>
    <h1>게시물 작성</h1>
    <hr>
    <form method="POST" action="" enctype="multipart/form-data">
        <label for="title"><strong>제목:</strong></label>
        <input type="text" id="title" name="title" required>
        <br>
        <label for="content">내용:</label>
        <textarea id="content" name="content" required></textarea>
        <br>
        <label for="file">파일 업로드(최대 5MB):</label>
        <input type="file" id="file" name="file">
        <br>
        <?php if ($error_message): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if ($file_error_message): ?>
            <p style="color: red;"><?php echo htmlspecialchars($file_error_message); ?></p>
        <?php endif; ?>
        <button type="submit">작성</button>
    </form>
</body>
</html>
