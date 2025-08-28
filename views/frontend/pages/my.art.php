<div class="textDiv layer top-left">
    My art page
</div>
<div class="textDiv layer top-mid">
    My art page
</div>
<div class="textDiv layer top-right">
    My art page
</div>
<div class="textDiv layer left">
    My art page
</div>
<div class="textDiv layer right">
    My art page
</div>
<div class="textDiv layer bottom-left">
    My art page
</div>
<div class="textDiv layer bottom-mid">
    My art page
</div>
<div class="textDiv layer bottom-right">
    My art page
</div>
<div class="textDiv toplayer">
    My art page
</div>

<div class="content-layout-art">

    <div class="masonry-wrapper-art">
        <div class="masonry-container-art">
            <?php $image_location = include __DIR__ . '/../../../src/Variables/image_location.php'; ?>
            <?php foreach ($entries as $entry): ?>
                <div class="masonry-item-art">
                    <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'post', 'post_id' => $entry['posts_id']]); ?>">
                        <img class="item_image-art" src="<?php echo $image_location; ?><?php echo e($entry['image_folder']); ?>" alt="Post image">
                    </a>
                    <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'post', 'post_id' => $entry['posts_id']]); ?>">
                        <h4 class="post-title-art"><?php echo e(ucfirst($entry['title'])); ?></h4>
                    </a>
                    <p class="post-description-art"><?php echo e(ucfirst($entry['content'])); ?></p>
                    <div class="post-categories-art">
                        <?php foreach ($entry['categories'] as $i => $cat_name): ?>
                            <?php if ($cat_name !== 'none') : ?>
                                <a class="category-badge-art" href="index.php?<?php echo http_build_query(['route' => 'client', 'pages' => 'category', 'category' => $cat_name, 'page' => 1]); ?>">
                                    <?php echo e($cat_name); ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="post-categories2-art">
                        <div class="post-owner-art">
                            By: <a href="index.php?<?php echo http_build_query(['route' => 'client', 'pages' => 'profile', 'nickname' => $entry['user_nickname'] ,'page' => 1]); ?>">
                            <?php echo e($entry['user_nickname']); ?>
                            </a>
                        </div>
                        <div class="hover-wrapper-art">
                            <img class="dots" src="./content/post/3dots_.png" alt="Options">
                            <div class="hover-info-art">
                                <span>Likes: <?php echo (int)$entry['likes']; ?></span><br>
                                <span>Comments: <?php echo (int)$entry['comments']; ?></span><br>
                                <span>Time: <?php echo $entry['time']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>
<br>
<?php $page = (int) $_GET['page'] ?? 1; ?>
<ul class="pagination floating-pagination">
<li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'myart', 'page' => 1]); ?>">&laquo;&laquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page === 1) : echo http_build_query(['route' => 'client' , 'pages' => 'myart', 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'myart', 'page' => ($page-1)]); endif; ?>">&laquo;
        </a>
    </li>
    <?php $page_shown = show_pages($num_pages, $page); ?>
    <?php foreach($page_shown AS $pag) : ?>
    <li class="pagination__li">
        <a class="pagination__link<?php if ($page === $pag) echo ' pagination__link--active'; ?>"
            href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'myart', 'page' => $pag]); ?>">
            <?php echo e($pag); ?>
        </a>
    </li>
    <?php endforeach; ?>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page == $num_pages) : echo http_build_query(['route' => 'client' , 'pages' => 'myart', 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'myart', 'page' => ($page+1)]); endif; ?>">&raquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'myart', 'page' => $num_pages]); ?>">&raquo;&raquo;</a>
    </li>
</ul>