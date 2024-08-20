<?php
require_once 'init.php';
require_once 'auth.php';

// 현재 사용자 ID
$user_id = $_SESSION['id'];

// 현재 사용자가 작성한 게시물 가져오기
$result = getUserPosts($mysqli, $user_id);

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>내가 쓴 글</title>
</head>
<body>
    <a href="index.php">메인으로</a>
    <hr>
    <h1>내가 쓴 글</h1>
    <hr>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <h2><a href="read_post.php?id=<?php echo htmlspecialchars($row['id']); ?>"><?php echo htmlspecialchars($row['title']); ?></a></h2>
            <p>게시일: <?php echo date('Y.m.d H:i', strtotime($row['created_at'])); ?>
                <?php if ($row['updated_at']): ?>
                    (수정일: <?php echo date('Y.m.d H:i', strtotime($row['updated_at'])); ?>)
                <?php endif; ?>
            </p>
            <p><?php echo htmlspecialchars(truncateContent($row['content'])); ?></p>

            <?php if ($row['file_path']): ?>
                <p>첨부파일: <?php echo htmlspecialchars(basename($row['file_path'])); ?></p>
            <?php else: ?>
                <p>첨부파일: 없음</p>
            <?php endif; ?>
            
            <hr>

        <?php endwhile; ?>
    <?php else: ?>
        <p>작성한 게시물이 없습니다.</p>
    <?php endif; ?>
</body>
</html>
