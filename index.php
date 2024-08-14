<?php
$mysqli = new mysqli("localhost", "root", "", "board");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// 모든 게시물 가져오기
$result = $mysqli->query("SELECT id, title, content, created_at, updated_at FROM posts ORDER BY created_at DESC");
$mysqli->close();

function truncateContent($content, $maxLength = 100){
    if (strlen($content) > $maxLength) {
        return substr($content, 0, $maxLength) . '...';
    } else {
        return $content;
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시판</title>
</head>
<body>
    <h1>게시판</h1>

    <!-- 검색 폼 -->
    <form method="GET" action="search.php">
        <input type="text" name="query" placeholder="검색어를 입력하세요" required>
        <button type="submit">검색</button>
    </form>

    <a href="create_post.php">새 글 쓰기</a>
    <hr>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <h2><a href="read_post.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h2>
            <p>게시일: <?php echo date('Y. m. d', strtotime($row['created_at'])); ?>
                <?php if ($row['updated_at']): ?>
                    (수정일: <?php echo date('Y. m. d', strtotime($row['updated_at'])); ?>)
                <?php endif; ?>
            </p>
            <p><?php echo htmlspecialchars(truncateContent($row['content'])); ?></p>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>게시물이 없습니다.</p>
    <?php endif; ?>
</body>
</html>
