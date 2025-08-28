<style>
.masonry-item {
    width: 50% !important;
}
</style>
<div class="content-layout">
    <div class="post-container-single">
        <div>
            Your followers:
        </div>
        <div class="masonry-container">
            <?php $image_location = include __DIR__ . '/../../../src/Variables/image_location.php'; ?>
            <?php foreach ($users as $user): ?>
                <div class="masonry-item">
                    <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname' => $user['nickname'], 'page' => 1]); ?>">
                        <img class="follower_image" src="<?php echo $image_location; ?><?php echo e($user['image_folder']); ?>" alt="Post image">
                    </a>
                    <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname' => $user['nickname'], 'page'=> 1]); ?>">
                        <h4 class="post-title"><?php echo e($user['nickname']); ?></h4>
                    </a>
                    <p class="post-description">
                        Motto: <?php echo e($user['motto']); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php $page = (int) $_GET['page'] ?? 1; ?>

<ul class="pagination floating-pagination">
<li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'followers', 'page' => 1]); ?>">&laquo;&laquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page === 1) : echo http_build_query(['route' => 'client' , 'pages' => 'followers', 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'followers', 'page' => ($page-1)]); endif; ?>">&laquo;
        </a>
    </li>
    <?php $page_shown = show_pages($num_pages, $page); ?>
    <?php foreach($page_shown AS $pag) : ?>
    <li class="pagination__li">
        <a class="pagination__link<?php if ($page === $pag) echo ' pagination__link--active'; ?>"
            href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'followers', 'page' => $pag]); ?>">
            <?php echo e($pag); ?>
        </a>
    </li>
    <?php endforeach; ?>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page == $num_pages) : echo http_build_query(['route' => 'client' , 'pages' => 'followers', 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'followers', 'page' => ($page+1)]); endif; ?>">&raquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'followers', 'page' => $num_pages]); ?>">&raquo;&raquo;</a>
    </li>
</ul>
