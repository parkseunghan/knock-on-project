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


// 업로드된 파일 경로 가져오기
$stmt = $mysqli->prepare("SELECT file_path FROM posts WHERE id = ?"); 
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($file_path);
$stmt->fetch();
$stmt->close();


// 게시물 삭제
$stmt = $mysqli->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();


// 파일 삭제
if ($file_path && file_exists($file_path)) {
    unlink($file_path);
}

$mysqli->close();

header("Location: index.php");
exit();
?>
