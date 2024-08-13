<?php
$mysqli = new mysqli("localhost", "root", "", "board");

if($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_GET['id']) && !is_array($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    echo "잘못된 요청입니다.";
    exit();
}


$stmt = $mysqli->prepare("SELECT title, content FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($title, $content);
$stmt->fetch();
$stmt->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // 게시물 업데이트
    $stmt = $mysqli->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);
    $stmt->execute();
    $stmt->close();


    echo "게시물이 성공적으로 수정되었습니다.";
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
