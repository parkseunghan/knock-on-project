<?php
// DB 설정
define('DB_SERVER', 'your_servername');
define('DB_USERNAME', 'your_username');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'your_dbname');

// 파일 업로드 설정
define('UPLOAD_DIR', '/your_project_root_path/uploads/'); // 업로드 경로
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 업로드 최대 허용 크기
define('ALLOWED_EXTS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']); // 업로드 허용 파일 형식

// 페이지 설정
define('POST_PER_PAGE', 7); // 페이지 당 게시물 수
define('MAX_LENGTH', 100); // 미리보기 내용

?>

    