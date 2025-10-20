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
        <?php foreach ($entries as $entry): ?>
            <div class="masonry-item">
                <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'post', 'post_id' => $entry['posts_id']]); ?>">
                    <img class="item_image" src="<?php echo $image_location; ?>/<?php echo e($entry['image_folder']); ?>" alt="Post image">
                </a>
                <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'post', 'post_id' => $entry['posts_id']]); ?>">
                    <h4 class="post-title"><?php echo e($entry['title']); ?></h4>
                </a>
                <p class="post-description"><?php echo e($entry['content']); ?></p>
                <div class="post-categories">
                    <?php $categories = explode(', ', $entry['categories']); ?>
                    <?php foreach ($categories as $i => $cat_name): ?>
                        <?php if ($cat_name !== 'none') : ?>
                            <a class="category-badge" href="index.php?<?php echo http_build_query(['route' => 'client', 'pages' => 'category', 'category' => trim($cat_name), 'page' => 1]); ?>">
                                <?php echo e(trim($cat_name)); ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="post-categories">
                    <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname' => $entry['user_nickname'] ,'page' => 1]); ?>">
                        By: <?php echo e($entry['user_nickname']); ?>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>
<ul class="pagination floating-pagination">
<li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'category', 'category' => $_GET['category'], 'page' => 1]); ?>">&laquo;&laquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page === 1) : echo http_build_query(['route' => 'client' , 'pages' => 'category', 'category' => $_GET['category'], 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'category', 'page' => ($page-1)]); endif; ?>">&laquo;
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
            href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'category', 'category' => $_GET['category'], 'page' => $pag]); ?>">
            <?php echo e($pag); ?>
        </a>
    </li>
    <?php endforeach; ?>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page == $num_pages) : echo http_build_query(['route' => 'client' , 'pages' => 'category', 'category' => $_GET['category'], 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'category', 'category' => $_GET['category'], 'page' => ($page+1)]); endif; ?>">&raquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'category', 'category' => $_GET['category'], 'page' => $num_pages]); ?>">&raquo;&raquo;</a>
    </li>
</ul>

<script> 
   const form = document.getElementById('searchForm');
   const input = document.getElementById('search_q');
</script>
<script src="./js/search_selector.js"></script>