<?php


// DB 연결 정보
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "board";

// MySQL 연결
$mysqli = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($mysqli->connect_error) {
    die("Connecion failed: " . $mysqli->connection_error);
}



// 적용
// create_post
// index
?>

