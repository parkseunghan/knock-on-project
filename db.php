<?php
require_once 'config.php';

// MySQL 연결
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// 연결 확인
if ($mysqli->connect_error) {
    die("Connecion failed: " . $mysqli->connection_error);
}




?>