<?php
require_once 'init.php';

// URL에서 게시물 ID 가져오기
if (isset($_GET['id']) && !is_array($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    echo "잘못된 요청입니다.";
    exit();
}

// 게시물 가져오기
$post = getPostById($mysqli, $id);
if (!$post) {
    echo "게시물이 존재하지 않습니다.";
    exit();
}

// 작성자 이름 가져오기
$author_username = getAuthorUsername($mysqli, $post['user_id']);
$mysqli->close();

$created_at = new DateTime($post['created_at']);
$updated_at = $post['updated_at'] ? new DateTime($post['updated_at']) : null;
$display_date = $created_at->format('Y. m. d H:i');

if ($updated_at) {
    $display_date .= ' (수정일: ' . $updated_at->format('Y. m. d H:i') . ')';
}

$is_author = isset($_SESSION['id']) && $_SESSION['id'] === $post['user_id'];
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
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <p>게시일: <?php echo htmlspecialchars($display_date); ?></p>
    <p>작성자: <?php echo htmlspecialchars($author_username ?: '작성자 정보 없음'); ?></p>

    <?php if ($is_author): ?>
        <a href="update_post.php?id=<?php echo $id; ?>">수정</a>
        <a href="delete_post.php?id=<?php echo $id; ?>" onclick="return confirmDeletion();">삭제</a>
    <?php endif; ?>

    <hr>
    <br>
    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
    <br>
  
    <?php if ($post['file_path']): ?>
    <hr>
    <p>첨부 파일: <a href="download.php?file=<?php echo urlencode($post['file_path']); ?>"><?php echo htmlspecialchars(basename($post['file_path'])); ?></a></p>
    <?php else: ?>
    <hr>
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
