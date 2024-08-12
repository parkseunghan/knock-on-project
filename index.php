<?php
    $mysqli= new mysqli("localhost", "root", "", "board");

    if($mysqli->connect_error) {
        die("Connetion failed: " . $mysqli->connect_error);
    }

    $result = $mysqli->query("SELECT * FROM posts");

    echo "<h1>게시판</h1>";
    while ($row = $result->fetch_assoc()) {
        echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
        echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
        echo "<hr>";
    }

    $mysqli->close();
?>
