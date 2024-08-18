<?php
require_once 'init.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}



if (isset($_GET['id']) && !is_array($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    echo "<script>alert('잘못된 요청입니다.'); window.location.href='index.php';</script>";
    exit();
}

// 게시물 데이터 가져오기
$stmt = $mysqli->prepare("SELECT user_id, file_path FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($post_user_id, $file_path);

if (!$stmt->fetch()) {
    echo "<script>alert('게시물이 존재하지 않습니다.'); window.location.href='index.php';</script>";
    exit();
}
$stmt->close();

// 게시물 작성자 확인
if ($_SESSION['id'] !== $post_user_id) {
    echo "<script>alert('게시물 삭제 권한이 없습니다.'); window.location.href='index.php';</script>";
    exit();
}

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

echo "<script>alert('게시물이 삭제되었습니다.'); window.location.href='index.php';</script>";
exit();
?>
