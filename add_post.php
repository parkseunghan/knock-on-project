<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $mysqli = new mysqli("localhost", "root", "", "board");

    if($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // 게시물 추가
    $stmt = $mysqli->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();

    // 메인으로 리다이렉트
    header("Location: index.php");
    exit();
}
?>

<form method="POST" action="">
    <label for="title">제목:</label>
    <input type="text" id="title" name="title" required>
    <br>
    <label for="content">내용:</label>
    <textarea id="content" name="content" required></textarea>
    <br>
    <button type="submit">게시</button>
</form>