<?php
    $mysqli= new mysqli("localhost", "root", "", "board");

    if($mysqli->connect_error) {
        die("Connetion failed: " . $mysqli->connect_error);
    }

    $result = $mysqli->query("SELECT * FROM posts");

    echo "<h1>게시판</h1>";
    while ($row = $result->fetch_assoc()) {
        echo "<h2><a href='view.php?id=" . $row.['id'] . "'>" . htmlspecialchars($row['title']) . "</a></h2>";
        echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
        echo "<hr>";
    }

    $mysqli->close();
?>

<a href="add_post.php">게시물 작성</a>
