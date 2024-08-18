<?php
header('Content-Type: text/html; charset=UTF-8');

$mysqli = new mysqli("localhost", "user", "user", "board");

// 데이터베이스 연결 오류 처리
if ($mysqli->connect_error) {
   die("Connection failed: " . $mysqli->connect_error);
}

// 사용자 이름 가져오기
$username = isset($_GET['username']) ? trim($_GET['username']) : '';

// 사용자 이름이 비어있지 않을 경우에만 처리
if ($username !== '') {
   // 사용자 이름 중복 확인
   $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
   $stmt->bind_param("s", $username);
   $stmt->execute();
   $stmt->store_result();

   if ($stmt->num_rows > 0) {
      echo "<span style='color: red;'>이미 존재하는 아이디입니다.</span>";
   } else {
      echo "<span style='color: green;'>사용 가능한 아이디입니다.</span>";
   }

   $stmt->close();
} else {
   // 사용자 이름이 비어있는 경우 오류 메시지 출력
   echo "<span style='color: red;'>아이디를 입력해 주세요.</span>";
}

$mysqli->close();
?>
