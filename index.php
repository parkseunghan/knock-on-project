<h1>게시판</h1>
<a href="create_post.php">새 글 작성</a>
<br>
<a href="search_post.php">게시물 검색</a>
<hr>
<?php

$mysqli = new mysqli("localhost", "root", "", "board");

if($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT id, title, content, file_path, created_at, updated_at FROM posts");

while ($post = $result->fetch_assoc()):
    $created_at = new DateTime($post['created_at']);
    $updated_at = new DateTime($post['updated_at']);
    $display_date = $created_at->format('Y. m. d H:i');

    if ($post['updated_at']) {
        $display_date .= ' (수정일: ' . $updated_at->format('Y. m. d H:i') . ')';
    }

?>
    <h2><a href="read_post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
    <p><?php echo htmlspecialchars($post['content']); ?></p>
    <p>게시일: <?php echo htmlspecialchars($display_date); ?></p>
    <?php if($post['file_path']): ?>
        <p>첨부 파일: <?php echo htmlspecialchars(basename($post['file_path'])); ?></p>
    <?php endif; ?>
    <hr>
<?php
endwhile;

$mysqli->close();
?>