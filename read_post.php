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
$stmt = $mysqli->prepare("SELECT title, content, file_path, created_at, updated_at FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($title, $content, $file_path, $created_at, $updated_at);
$stmt->fetch();
$stmt->close();
$mysqli->close();

$created_at = new DateTime($created_at);
$updated_at = $updated_at ? new DateTime($updated_at) : null;
$display_date = $created_at->format('Y. m. d H:i');

if ($updated_at) {
    $display_date .= ' (수정일: ' . $updated_at->format('Y. m. d H:i') . ')';
}
?>



<a href="index.php">메인으로</a>
<hr>
<h1><?php echo htmlspecialchars($title); ?></h1>
<p>게시일: <?php echo htmlspecialchars($display_date); ?></p>
<a href="update_post.php?id=<?php echo $id; ?>">수정</a>
<a href="delete_post.php?id=<?php echo $id; ?>" onclick="return confirmDeletion();">삭제</a>
<hr>
<br>
<p><?php echo htmlspecialchars($content); ?></p>
<br>
<?php if ($file_path): ?>
    <hr>
    <p>첨부 파일: <a href="<?php echo $file_path; ?>" target="_blank"><?php echo basename($file_path); ?></a></p>
<?php endif; ?>

<hr>

<script>
    function confirmDeletion() { 
        return confirm("정말로 삭제하시겠습니까?");
    }
</script>