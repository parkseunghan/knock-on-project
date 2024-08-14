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

// 기존 게시물 데이터 가져오기
$stmt = $mysqli->prepare("SELECT title, content, file_path FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($title, $content, $existing_file_path);

if (!$stmt->fetch()) {
    echo "게시물이 존재하지 않습니다.";
    exit();
}

$stmt->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $upload_dir = __DIR__ . '/uploads/';
    $file_path = $existing_file_path; 

    // 파일 업로드
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_name = time() . '_' . uniqid() . '_' .  basename($_FILES['file']['name']);
        $new_file_path = $upload_dir . $file_name;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $new_file_path)) {
            // 기존 파일이 있으면 삭제
            if ($existing_file_path && file_exists($existing_file_path)) {
                unlink($existing_file_path);
            }
            $file_path = $new_file_path;
        } else {
            echo "파일 업로드 실패!";
            exit();
        }
    }

    // 게시물 업데이트
    $stmt = $mysqli->prepare("UPDATE posts SET title = ?, content = ? , file_path = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $content, $file_path, $id);
    $stmt->execute();
    $stmt->close();


    echo "<script>alert('게시물이 수정되었습니다.'); window.location.href='index.php';</script>";
    exit();
}

$result = $mysqli->query("SELECT * FROM posts WHERE id = $id");
$post = $result->fetch_assoc();
$mysqli->close();
?>


<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시물 수정</title>
</head>

<body>
    <a href="index.php">메인으로</a>

    <h1>게시물 수정</h1>

    <hr>
    <form method="POST" action="" enctype="multipart/form-data">
        <label for="title"><strong>제목:</strong></label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
        <br>
        <label for="content">내용:</label>
        <textarea id="content" name="content" required><?php echo htmlspecialchars($content); ?></textarea>
        <br>
        <label for="file">파일 업로드:</label>
        <input type="file" id="file" name="file">
        <br>
        <?php if ($file_path): ?>
        <p>현재 업로드된 파일:
            <?php echo htmlspecialchars(basename($file_path)); ?>
        </p>
        <?php endif; ?>
        <button type="submit">수정</button>
    </form>
    <a href="delete_post.php?id=<?php echo $id; ?>" onclick="return confirmDeletion();">삭제</a>

    <script>
        function confirmDeletion() {
            return confirm("정말로 삭제하시겠습니까?");
        }
    </script>
</body>

</html>