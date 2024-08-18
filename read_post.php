<?php
require_once 'init.php';

// URL에서 게시물 ID 가져오기
if (isset($_GET['id']) && !is_array($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    echo "잘못된 요청입니다.";
    exit();
}

// SQL 쿼리로 해당 게시물 가져오기
$stmt = $mysqli->prepare("SELECT title, content, file_path, created_at, updated_at, user_id FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($title, $content, $file_path, $created_at, $updated_at, $post_user_id);

if (!$stmt->fetch()) {
    echo "게시물이 존재하지 않습니다.";
    exit();
}

$stmt->close();

// 작성자 이름 가져오기
$stmt = $mysqli->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $post_user_id);
$stmt->execute();
$stmt->bind_result($author_username);
$stmt->fetch();
$stmt->close();

$mysqli->close();

$created_at = new DateTime($created_at);
$updated_at = $updated_at ? new DateTime($updated_at) : null;
$display_date = $created_at->format('Y. m. d H:i');

if ($updated_at) {
    $display_date .= ' (수정일: ' . $updated_at->format('Y. m. d H:i') . ')';
}

$is_author = isset($_SESSION['id']) && $_SESSION['id'] === $post_user_id;
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시물 읽기</title>
</head>
<body>
    <a href="index.php">메인으로</a>
    <hr>
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <p>게시일: <?php echo htmlspecialchars($display_date); ?></p>
    <p>작성자: <?php echo htmlspecialchars($author_username ?: '작성자 정보 없음'); ?></p>

    <?php if ($is_author): ?>
        <a href="update_post.php?id=<?php echo $id; ?>">수정</a>
        <a href="delete_post.php?id=<?php echo $id; ?>" onclick="return confirmDeletion();">삭제</a>
    <?php endif; ?>

    <hr>
    <br>
    <p><?php echo nl2br(htmlspecialchars($content)); ?></p>
    <br>
  

    <?php if ($file_path): ?>
    <hr>
    <p>첨부 파일: <a href="download.php?file=<?php echo urlencode($file_path); ?>"><?php echo htmlspecialchars(basename($file_path)); ?></a></p>
    <?php else: ?>
    <p>첨부 파일: 없음</p>
    <?php endif; ?>

    <hr>

    <script>
        function confirmDeletion() { 
            return confirm("정말로 삭제하시겠습니까?");
        }
    </script>
</body>
</html>
