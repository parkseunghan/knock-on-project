<?php
require_once 'init.php';
require_once 'auth.php';

// 게시물 ID 검증
if (isset($_GET['id']) && !is_array($_GET['id'])) {
    $id = intval($_GET['id']); // ID를 정수로 변환
} else {
    echo "<script>alert('잘못된 요청입니다.'); window.location.href='index.php';</script>";
    exit();
}

// 게시물 삭제
$result = deletePost($mysqli, $id, $_SESSION['id']);

// 결과 처리
if ($result['success']) {
    echo "<script>alert('" . $result['message'] . "'); window.location.href='index.php';</script>";
} else {
    echo "<script>alert('" . $result['message'] . "'); window.location.href='index.php';</script>";
}

$mysqli->close();
exit();
?>
