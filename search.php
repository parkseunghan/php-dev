<?php
require_once 'init.php'; // init.php에 이미 functions.php가 포함되어 있다고 가정
require_once 'auth.php';

$query = isset($_GET['query']) ? $mysqli->real_escape_string($_GET['query']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = ($page > 0) ? $page : 1; // 페이지가 1보다 작은 경우 1로 설정

// 페이지당 게시물 수
$post_per_page = POST_PER_PAGE;
$offset = ($page - 1) * $post_per_page;

// 총 게시물 수 계산
$total_posts = getTotalPosts($mysqli, $query);

// 검색 결과 가져오기
$result = getPosts($mysqli, $offset, $post_per_page, $query);

$mysqli->close();

$total_pages = ceil($total_posts / $post_per_page);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>검색 결과</title>
</head>
<body>
    <a href="index.php">메인으로</a>
    <hr>
    <h1>검색 결과</h1>

    <?php if ($query): ?>
        <p>검색어: <strong><?php echo htmlspecialchars($query); ?></strong></p>
    <?php endif; ?>

    <hr>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <h2><a href="read_post.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h2>
            <p>게시일: <?php echo date('Y.m.d H:i', strtotime($row['created_at'])); ?>
                <?php if ($row['updated_at']): ?>
                    (수정일: <?php echo date('Y.m.d H:i', strtotime($row['updated_at'])); ?>)
                <?php endif; ?>
            </p>
            <p><?php echo htmlspecialchars(truncateContent($row['content'])); ?></p>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>검색 결과가 없습니다.</p>
    <?php endif; ?>

    <!-- 페이지 네비게이션 -->
    <nav aria-label="Page navigation">
        <ul>
            <?php if ($page > 1): ?>
                <li><a href="?query=<?php echo urlencode($query); ?>&page=<?php echo $page - 1; ?>">이전</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li>
                    <a href="?query=<?php echo urlencode($query); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li><a href="?query=<?php echo urlencode($query); ?>&page=<?php echo $page + 1; ?>">다음</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</body>
</html>
