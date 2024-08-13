<?php
$mysqli = new mysqli("localhost", "root", "", "board");

if($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$search = $_GET['search'] ?? '';
$result = $mysqli->query("SELECT * FROM posts WHERE title LIKE '%" . $mysqli->real_escape_string($search) . "%'");

echo "<h1>검색 결과</h1>";
while ($row = $result->fetch_assoc()) {
    echo "<h2><a href='view.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['title']) . "</a></h2>";
    echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
    echo "<hr>";
}

$mysqli->close();
?>

<form method="GET" action="">
    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" required>
    <button type="submit">검색</button>
</form>