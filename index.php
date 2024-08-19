<?php
require_once 'init.php';

// 사용자 인증 확인
if (!isset($_SESSION['id']) && isset($_COOKIE['id']) && isset($_COOKIE['username'])) {
    $_SESSION['id'] = $_COOKIE['id'];
    $_SESSION['username'] = $_COOKIE['username'];
}

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$post_per_page = POST_PER_PAGE;

// 현재 페이지
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = max(1, $page); // 페이지가 1보다 작은 경우 1로 설정
$offset = ($page - 1) * $post_per_page;

// 총 게시물 수 계산
$total_posts = getTotalPosts($mysqli, NULL);

// 총 페이지 수 계산
$total_pages = ceil($total_posts / $post_per_page);

// 게시물 가져오기
$result = getPosts($mysqli, $offset, $post_per_page, NULL);
$mysqli->close();
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

    <nav>
        <a href="create_post.php">새 글 쓰기</a> |
        <a href="user_posts.php">내가 쓴 글</a> |
        <a href="update_profile.php">프로필 수정</a> |
        <a href="logout.php">로그아웃</a>
    </nav>

    <hr>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <h2>
                <a href="read_post.php?id=<?php echo htmlspecialchars($row['id']); ?>">
                    <?php echo htmlspecialchars($row['title']); ?>
                </a>
            </h2>
            
            <p>게시일: <?php echo date('Y. m. d', strtotime($row['created_at'])); ?>

                <?php if ($row['updated_at']): ?>

                    (수정일: <?php echo date('Y. m. d', strtotime($row['updated_at'])); ?>)

                <?php endif; ?>
            </p>

            <p>작성자: <?php echo htmlspecialchars($row['username'] ?? '작성자 정보 없음'); ?></p>

            <p><?php echo htmlspecialchars(truncateContent($row['content'])); ?></p>

            <?php if ($row['file_path']): ?>
                <p>첨부파일: <?php echo htmlspecialchars(basename($row['file_path'])); ?></p>

            <?php else: ?>

                <p>첨부파일: 없음</p>

            <?php endif; ?>

            <hr>
        <?php endwhile; ?>

    <?php else: ?>

        <p>게시물이 없습니다.</p>

    <?php endif; ?>

    <!-- 페이지 네비게이션 -->
    <nav>
        <ul>
            <?php if ($page > 1): ?>
                <li><a href="?page=<?php echo $page - 1; ?>">이전</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li>
                    <a href="?page=<?php echo $i; ?>" <?php if ($i === $page) echo 'style="font-weight:bold;"'; ?>><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li><a href="?page=<?php echo $page + 1; ?>">다음</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</body>
</html>
