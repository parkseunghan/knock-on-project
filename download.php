<?php
session_start();

// 사용자가 로그인했는지 확인
if (!isset($_SESSION['id'])) {
    echo "로그인이 필요합니다.";
    exit();
}

if (isset($_GET['file']) && !is_array($_GET['file'])) {
    $file_path = $_GET['file'];
    
    // 파일 경로를 기반으로 파일명 추출
    $file_name = basename($file_path);

    // 파일이 존재하는지 확인
    if (file_exists($file_path)) {
        // 파일 다운로드에 필요한 헤더 설정
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Length: ' . filesize($file_path));
        
        // 파일 읽고 출력
        readfile($file_path);
        exit();
    } else {
        echo "파일이 존재하지 않습니다.";
    }
} else {
    echo "잘못된 요청입니다.";
}
?>
