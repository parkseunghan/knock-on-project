<?php
$mysqli = new $mysqli("localhost", "root", "", "board");

if($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // 게시물 업데이트
    $stmp = $mysqli->prepare("UPDATE posts SET title = ?, content = ?, WHERE id = ?");
    $stmp->bind_param("ssi", $title, $content, $id);
    $stmp->execute();
    $stmp->close();

    header("Location: index.php");
    exit();
}

$result = $mysqli->query("SELECT * FROM posts WHERE id = $id");
$post = $result->fetch_assoc();
$mysqli->close();
?>

<form method="POST" action="">
    <label for="title">제목:</label>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
    <br>
    <label for="content">내용:</label>
    <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
    <br>
    <button type="submit">수정</button>
</form>
<a href="delete_post.php?id=<?php echo $id; ?>">삭제</a>
