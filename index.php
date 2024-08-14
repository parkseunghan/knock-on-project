<?php
session_start();

if(!isset($_SESSION['id']) && isset($_COOKIE['id']) && isset($_COOKIE['username'])){
    $_SESSION['id'] = $_COOKIE['id'];
    $_SESSION['username'] = $_COOKIE['username'];
}

if(!isset($_SESSION['id'])){
    header('Location: login.php');
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "board");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// 페이지당 게시물 수
$post_per_page = 7;

// 현재 페이지
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = ($page > 0) ? $page : 1; // 페이지가 1보다 작은 경우 1로 설정
$offset = ($page - 1) * $post_per_page;

// 총 게시물 수 계산
$total_stmt = $mysqli->prepare("SELECT COUNT(*) FROM posts");
$total_stmt->execute();
$total_stmt->bind_result($total_posts);
$total_stmt->fetch();
$total_stmt->close();

// 총 페이지 수 계산
$total_pages = ceil($total_posts / $post_per_page);

// 게시물 가져오기
$stmt = $mysqli->prepare("SELECT id, title, content, created_at, updated_at FROM posts ORDER BY created_at DESC LIMIT ?, ?");
$stmt->bind_param("ii", $offset, $post_per_page);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$mysqli->close();

function truncateContent($content, $maxLength = 100) {
    if (strlen($content) > $maxLength) {
        return substr($content, 0, $maxLength) . '...';
    } else {
        return $content;
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시판</title>
</head>
<body>
    <a href="index.php"><h1>게시판</h1></a>

    <!-- 검색 폼 -->
    <form method="GET" action="search.php">
        <input type="text" name="query" placeholder="검색어를 입력하세요" required>
        <button type="submit">검색</button>
    </form>

    <a href="create_post.php">새 글 쓰기</a>
    <a href="logout.php">로그아웃</a>

    <hr>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <h2><a href="read_post.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h2>
            <p>게시일: <?php echo date('Y. m. d', strtotime($row['created_at'])); ?>
                <?php if ($row['updated_at']): ?>
                    (수정일: <?php echo date('Y. m. d', strtotime($row['updated_at'])); ?>)
                <?php endif; ?>
            </p>
            <p><?php echo htmlspecialchars(truncateContent($row['content'])); ?></p>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>게시물이 없습니다.</p>
    <?php endif; ?>

    <!-- 페이지 네비게이션 -->
    <nav aria-label="Page navigation">
        <ul>
            <?php if ($page > 1): ?>
                <li><a href="?page=<?php echo $page - 1; ?>">이전</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li>
                    <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li><a href="?page=<?php echo $page + 1; ?>">다음</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</body>
</html>
