<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $upload_dir = __DIR__ . '/uploads/';
    $file_path = '';

    $mysqli = new mysqli("localhost", "root", "", "board");

    if($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // 파일 업로드
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_name = time() . '_' . uniqid() . '_' . basename($_FILES['file']['name']);
        $new_file_path = $upload_dir . $file_name;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);     
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $new_file_path)) {
            $file_path = $new_file_path;
        } else {
            echo "파일 업로드 실패!";
            exit();
        }
    }

    // 게시물 추가
    $stmt = $mysqli->prepare("INSERT INTO posts (title, content, file_path) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $content, $file_path);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();

    // 메인으로 리다이렉트
    echo "<script>alert('게시물이 작성되었습니다.'); window.location.href='index.php';</script>";
    exit();
}
?>

<a href="index.php">메인으로</a>
<hr>
<form method="POST" action="" enctype = "multipart/form-data">
    <label for="title">제목:</label>
    <input type="text" id="title" name="title" required>
    <br>
    <label for="content">내용:</label>
    <textarea id="content" name="content" required></textarea>
    <br>
    <label for="content">파일 업로드:</label>
    <input type="file" id="file" name="file">
    <br>
    <button type="submit">게시</button>
</form>


