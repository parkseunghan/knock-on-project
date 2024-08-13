<?php
$mysqli = new $mysqli("localhost", "root", "", "board");

if($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$id = $_GET['id'];

// 게시물 삭제
$stmp = $mysqli->prepare("DELETE FROM posts WHERE id = ?");
$stmp->bind_param("i", $id);
$stmp->execute();
$stmp->close();

$mysqli->close();

header("Location: index.php");
exit();
?>
