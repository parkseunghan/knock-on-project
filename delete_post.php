<?php
$mysqli = new mysqli("localhost", "root", "", "board");

if($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$id = $_GET['id'];

// 게시물 삭제
$stmt = $mysqli->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$mysqli->close();

header("Location: index.php");
exit();
?>
