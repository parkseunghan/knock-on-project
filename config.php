<?php
// DB 설정
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'board');

// 파일 업로드 경로
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// 파일 업로드 최대 크기
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// 파일 업로드 허용 형식
define('ALLOWED_EXTS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'c', 'sql']);

?>

