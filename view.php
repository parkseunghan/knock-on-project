<?php
// 데이터베이스 연결
$mysqli = new mysqli("localhost", "root", "", "board");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// URL에서 게시물 ID 가져오기
if (isset($_GET['id']) && !is_array($_GET['id'])){
    $id = intval($_GET['id']);
} else {
    echo "잘못된 요청입니다.";
    exit();
}

// SQL 쿼리로 해당 게시물 가져오기
$stmt = $mysqli->prepare("SELECT title, content, created_at FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($title, $content, $created_at);
$stmt->fetch();
$stmt->close();
$mysqli->close();

if ($title) {
    echo "<h1>" . htmlspecialchars($title) . "</h1>";
    echo "<p>작성일: " . $created_at . "</p>";
    echo "<p>" . nl2br(htmlspecialchars($content)) . "</p>";
} else {
    echo "<p>해당 게시물을 찾을 수 없습니다.</p>";
}
?>

<a href="index.php">메인화면으로 돌아가기</a>
<a href="edit_post.php?id=<?php echo $id; ?>">게시물 수정</a>
<a href="delete_post.php?id=<?php echo $id; ?>">게시물 삭제</a>
