<?php
header('Content-Type: application/xml; charset=utf-8');
ob_clean();

require_once __DIR__ . '/../../inc/functions.inc.php';
require_once __DIR__ . '/../../inc/db-connect.inc.php';

$user_nickname = ($_GET['nickname'] ?? 'Anna');
$page = ($_GET['page'] ?? 1);

$userStmt = $pdo->prepare('SELECT * FROM users WHERE nickname = :nickname');
$userStmt->bindValue(':nickname', $user_nickname);
$userStmt->execute();
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

$commentsStmt = $pdo->prepare('SELECT COUNT(*) as `count` FROM comments WHERE users_nickname = :users_nickname');
$commentsStmt->bindValue(':users_nickname', $user_nickname);
$commentsStmt->execute();
$comments = $commentsStmt->fetch(PDO::FETCH_ASSOC);
$commentCount = (int)$comments['count'];

$likesStmt = $pdo->prepare('SELECT COUNT(*) as `count` FROM likes WHERE users_id = :users_id');
$likesStmt->bindValue(':users_id', $user['users_id']);
$likesStmt->execute();
$likes = $likesStmt->fetch(PDO::FETCH_ASSOC);
$likesCount = (int)$likes['count'];

$postsStmt = $pdo->prepare('SELECT COUNT(*) as `count` FROM posts WHERE user_nickname = :user_nickname');
$postsStmt->bindValue(':user_nickname', $user_nickname);
$postsStmt->execute();
$postsCount = $postsStmt->fetch(PDO::FETCH_ASSOC);
$postsCount = (int)$postsCount['count'];

$perPage = 10;
$num_pages = ceil($postsCount/$perPage);

$offset = ($page-1)*$perPage;
$stmt = $pdo->prepare('SELECT posts.posts_id, posts.user_nickname, posts.title, posts.content, posts.image_folder, 
            posts.likes, posts.comments, DATE_FORMAT(posts.`time`, "%Y-%m-%dT%H:%i:%s") AS `time`,
            GROUP_CONCAT(percategory.category_title ORDER BY percategory.category_title SEPARATOR ", ") AS categories
            FROM posts
            LEFT JOIN percategory ON percategory.post_id = posts.posts_id
            WHERE deleted = :deleted AND status = :status AND posts.user_nickname = :user_nickname
            GROUP BY posts.posts_id
            ORDER BY posts.posts_id DESC
            LIMIT :perPage OFFSET :offset');

$stmt->bindValue(':deleted', false, PDO::PARAM_BOOL);
$stmt->bindValue(':status', 'public');
$stmt->bindValue(':user_nickname', $user_nickname);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

function escapeXml($string) {
    return htmlspecialchars($string, ENT_XML1, 'UTF-8');
}

$baseUrl = "http://localhost/blog-type-site/src/XMLFeed/XMLFeed_user.php";
$paginationLinks = [
    'first' => $baseUrl . "?nickname=" . urlencode($user_nickname) . "&page=1",
    'prev' => $baseUrl . "?nickname=" . urlencode($user_nickname) . "&page=" . ($page === 1 || $page < 1 ? 1 : $page - 1),
    'pages' => [],
    'next' => $baseUrl . "?nickname=" . urlencode($user_nickname) . "&page=" . ($page == $num_pages ? $page : $page + 1),
    'last' => $baseUrl . "?nickname=" . urlencode($user_nickname) . "&page=" . $num_pages
];

$page_shown = show_pages($num_pages, $page);
foreach ($page_shown as $pag) {
    $paginationLinks['pages'][] = [
        'number' => $pag,
        'url' => $baseUrl . "?nickname=" . urlencode($user_nickname) . "&page=" . $pag,
        'active' => ($pag == $page)
    ];
}

// xml sheet starts here

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="feed-style-user.xsl"?>';
?>
<pins xmlns:media="http://search.yahoo.com/mrss/">
    <metadata>
        <site>Site</site>
        <url>https://localhost/blog-type-site/index.php?route=client&amp;pages=browse</url>
        <description>Latest pins from <?php echo escapeXml($user_nickname); ?></description>
        <generated><?php echo date('c'); ?></generated>
        <count><?php echo count($posts); ?></count>
    </metadata>

    <info>Posts from:</info>
    <!-- User Profile Section -->
    <user_profile>
        <name><?php echo escapeXml($user['nickname'] ?? $user_nickname); ?></name>
        <?php if (isset($user['image_folder'])): ?>
        <avatar>https://localhost/blog-type-site/<?php echo escapeXml($user['image_folder']); ?></avatar>
        <?php endif; ?>
        <?php if (isset($user['motto'])): ?>
        <bio><![CDATA[<?php echo $user['motto']; ?>]]></bio>
        <?php endif; ?>
        <profile_url>https://localhost/blog-type-site/index.php?route=client&amp;pages=profile&amp;nickname=<?php echo escapeXml($user_nickname); ?></profile_url>
        <stats>
            <posts_count><?php echo $postsCount; ?></posts_count>
            <?php if (isset($likesCount)): ?>
            <likes><?php echo $likesCount; ?></likes>
            <?php endif; ?>
            <?php if (isset($comments)): ?>
            <comments><?php echo $commentCount; ?></comments>
            <?php endif; ?>
        </stats>
    </user_profile>
    
    <!-- Posts Section -->
    <?php foreach ($posts as $post): ?>
    <pin>
        <id><?php echo $post['posts_id']; ?></id>
        <title><?php echo escapeXml($post['title']); ?></title>
        <description><![CDATA[<?php echo $post['content']; ?>]]></description>
        <url>https://localhost/blog-type-site/index.php?route=client&amp;pages=post&amp;post=<?php echo $post['posts_id']; ?></url>
        <created><?php echo escapeXml($post['time']); ?></created>
        
        <image>
            <url>https://localhost/blog-type-site/<?php echo escapeXml($post['image_folder']); ?></url>
        </image>
        
        <author>
            <name><?php echo escapeXml($post['user_nickname']); ?></name>
            <profile>https://localhost/blog-type-site/index.php?route=client&amp;pages=profile&amp;nickname=<?php echo escapeXml($post['user_nickname']); ?></profile>
        </author>
        
        <stats>
            <likes><?php echo $post['likes'] ?? 0; ?></likes>
            <comments><?php echo $post['comments'] ?? 0; ?></comments>
        </stats>
        
        <?php if (!empty($post['categories'])): ?>
        <categories>
            <?php $categories = explode(',', $post['categories']); ?>
            <?php foreach ($categories as $cat): ?>
                <?php if($cat !== 'None' && $cat !== 'none') : ?> 
                    <category><?php echo escapeXml(trim($cat)); ?></category>
                <?php endif; ?>
            <?php endforeach; ?>
        </categories>
        <?php endif; ?>
    </pin>
    <?php endforeach; ?>

    <pagination>
        <first><![CDATA[<?php echo $paginationLinks['first']; ?>]]></first>
        <previous><![CDATA[<?php echo $paginationLinks['prev']; ?>]]></previous>
        <pages>
            <?php foreach ($paginationLinks['pages'] as $pageLink): ?>
            <page active="<?php echo $pageLink['active'] ? 'true' : 'false'; ?>">
                <number><?php echo $pageLink['number']; ?></number>
                <url><![CDATA[<?php echo $pageLink['url']; ?>]]></url>
            </page>
            <?php endforeach; ?>
        </pages>
        <next><![CDATA[<?php echo $paginationLinks['next']; ?>]]></next>
        <last><![CDATA[<?php echo $paginationLinks['last']; ?>]]></last>
    </pagination>
</pins>
