<?php
$mysqli = new mysqli("localhost", "root", "", "board");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$query = isset($_GET['query']) ? $mysqli->real_escape_string($_GET['query']) : '';

if ($query) {
    $stmt = $mysqli->prepare("SELECT id, title, content, created_at, updated_at FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC");
    $search_term = '%' . $query . '%';
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = $mysqli->query("SELECT id, title, content, created_at, updated_at FROM posts ORDER BY created_at DESC");
}

function truncateContent($content, $maxLength = 100){
    if (strlen($content) > $maxLength) {
        return substr($content, 0, $maxLength) . '...';
    } else {
        return $content;
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>검색 결과</title>
</head>
<body>
    <a href="index.php">메인으로</a>
    <hr>
    <h1>검색 결과</h1>

    <?php if ($query): ?>
        <p>검색어: <strong><?php echo htmlspecialchars($query); ?></strong></p>
    <?php endif; ?>

    <hr>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <h2><a href="read_post.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h2>
            <p>게시일: <?php echo date('Y.m.d H:i', strtotime($row['created_at'])); ?>
                <?php if ($row['updated_at']): ?>
                    (수정일: <?php echo date('Y.m.d H:i', strtotime($row['updated_at'])); ?>)
                <?php endif; ?>
            </p>
            <p><?php echo htmlspecialchars(truncateContent($row['content'])); ?></p>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>검색 결과가 없습니다.</p>
    <?php endif; ?>
</body>
</html>
