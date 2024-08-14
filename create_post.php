<?php
session_start();

// 사용자 인증 확인
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $upload_dir = __DIR__ . '/uploads/';
    $file_path = '';

    // 데이터베이스 연결
    $mysqli = new mysqli("localhost", "root", "", "board");

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // 파일 업로드 처리
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // 파일 용량 제한 
        $max_file_size = 10 * 1024 * 1024; 
        if ($_FILES['file']['size'] > $max_file_size) {
            echo "파일 용량 초과(최대 10MB)";
            exit();
        }

        // 허용된 파일 확장자
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'c', 'sql'];
        $file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_exts)) {
            echo "허용되지 않는 파일 형식입니다.";
            exit();
        }

        // 파일 경로 및 이름 설정
        $file_name = time() . '_' . uniqid() . '_' . basename($_FILES['file']['name']);
        $new_file_path = $upload_dir . $file_name;

        // 업로드 디렉토리 생성
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // 파일 이동 및 저장
        if (move_uploaded_file($_FILES['file']['tmp_name'], $new_file_path)) {
            $file_path = $new_file_path;
        } else {
            echo "파일 업로드 실패!";
            exit();
        }
    } elseif (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo "파일 업로드 중 오류가 발생했습니다.";
        exit();
    }

    // 게시물 추가
    $user_id = $_SESSION['id']; // 현재 로그인한 사용자의 ID
    $stmt = $mysqli->prepare("INSERT INTO posts (title, content, file_path, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $content, $file_path, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('게시물이 작성되었습니다.'); window.location.href='index.php';</script>";
    } else {
        echo "게시물 작성 실패!";
    }

    $stmt->close();
    $mysqli->close();
    exit();
}
?>

<a href="index.php">메인으로</a>
<hr>
<h1>새 글 쓰기</h1>
<hr>
<form method="POST" action="" enctype="multipart/form-data">
    <label for="title">제목:</label>
    <input type="text" id="title" name="title" required>
    <br>
    <label for="content">내용:</label>
    <textarea id="content" name="content" required></textarea>
    <br>
    <label for="file">파일 업로드:</label>
    <input type="file" id="file" name="file">
    <br>
    <button type="submit">게시</button>
</form>
