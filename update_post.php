<?php
require_once 'init.php';
require_once 'auth.php';


//게시물 ID 검증
$id = isset($_GET['id']) && !is_array($_GET['id']) ? intval($_GET['id']) : null;
if ($id === null) {
    echo "잘못된 요청입니다.";
    exit();
}

// 게시물 데이터 가져오기
$post = fetchPost($id);
if (!$post) {
    echo "게시물이 존재하지 않습니다.";
    exit();
}

// 로그인한 사용자가 게시물의 작성자인지 확인
// if ($_SESSION['id'] !== $post['user_id']) {
//     echo "게시물 수정 권한이 없습니다.";
//     exit();
// }

$file_path = $post['file_path'];
$error_message = '';
$file_error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['title']) && !empty($_POST['content'])) {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        // 파일 업로드 처리
        $file_result = handleFileUpload($file_path);
        if (is_array($file_result)) {
            $file_path = $file_result['file_path'];
            $file_error_message = $file_result['error_message'];
        }

        if (empty($file_error_message)) {
            // 게시물 업데이트
            if (updatePost($id, $title, $content, $file_path)) {
                echo "<script>alert('게시물이 수정되었습니다.'); window.location.href='index.php';</script>";
                exit();
            } else {
                $error_message = "게시물 수정 실패!";
            }
        }
    } else {
        $error_message = "제목과 내용을 입력해야 합니다.";
    }
}

// 게시물 데이터 재조회
$post = fetchPost($id);
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
        <label for="file">파일 업로드(최대 5MB):</label>
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
