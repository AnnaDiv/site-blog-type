<div class="top-bar">
    <div class="search-bar">
        <form id="searchForm" method="GET">
            <input type="text" pattern=".{3,}" name="search_q" id="search_q"
                   value="<?php if (!empty($_GET['search_q'])) echo e($_GET['search_q']); ?>"
                   required title="3 characters minimum" placeholder="Search for..." />

                <button type="submit" class="user-search-btn" data-target="posts">Posts</button>
                <button type="submit" class="user-search-btn" data-target="users">Users</button>
        </form>
    </div>
</div>

<div class="content-layout">

    <div class="masonry-container">
        <?php $image_location = include __DIR__ . '/../../../src/Variables/image_location.php'; ?>
        <?php foreach ($users as $user): ?>
            <div class="masonry-item">
                <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname' => $user['nickname'], 'page' => 1]); ?>">
                    <img class="item_image" src="<?php echo $image_location; ?><?php echo e($user['image_folder']); ?>" alt="Post image">
                </a>
                <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname' => $user['nickname'], 'page'=> 1]); ?>">
                    <h4 class="post-title"><?php echo e($user['nickname']); ?></h4>
                </a>
                <p class="post-description">
                    Motto: <?php echo e($user['motto']); ?>
                </p>
                <div class="post-categories">
                    Posts: <?php echo e($user['posts_count']); ?><br>
                    Comments: <?php echo e($user['comments_count']); ?><br>
                    Likes: <?php echo e($user['likes_count']); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>
<?php $page = (int) $_GET['page'] ?? 1; ?>
<ul class="pagination floating-pagination">
<li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'search_user', 'page' => 1]); ?>">&laquo;&laquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page === 1) : echo http_build_query(['route' => 'client' , 'pages' => 'search_user', 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'search_user', 'page' => ($page-1)]); endif; ?>">&laquo;
        </a>
    </li>
    <?php $page_shown = show_pages($num_pages, $page); ?>
    <?php foreach($page_shown AS $pag) : ?>
    <li class="pagination__li">
        <a class="pagination__link  
            <?php if ($page === $pag) : ?> 
                pagination__link--active
                <?php $current_active_page = $pag; ?> 
            <?php endif; ?>" 
            href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'search_user', 'page' => $pag]); ?>">
            <?php echo e($pag); ?>
        </a>
    </li>
    <?php endforeach; ?>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page == $num_pages) : echo http_build_query(['route' => 'client' , 'pages' => 'search_user', 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'search_user', 'page' => ($page+1)]); endif; ?>">&raquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'search_user', 'page' => $num_pages]); ?>">&raquo;&raquo;</a>
    </li>
</ul>

<script> 
   const form = document.getElementById('searchForm');
   const input = document.getElementById('search_q');
</script>
<script src="./js/search_selector.js"></script>