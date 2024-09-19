<?php
if (!isset($_SESSION['id']) && isset($_COOKIE['id']) && isset($_COOKIE['username'])) {
    $_SESSION['id'] = $_COOKIE['id'];
    $_SESSION['username'] = $_COOKIE['username'];
}

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
} # dfs
?>