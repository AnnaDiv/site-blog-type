<div class="user_Profile">
    <div class="user_Profile-info">
        <div class="user_Profile-info__name">
            <h4>Managing profile for: </h4>
            <h2 class="username">User: <?php echo $user->nickname; ?></h2>
            <?php $image_location = include __DIR__ . '/../../../src/Variables/image_location.php'; ?>
            <img class="user_Profile-info__image" src="<?php echo $image_location; ?><?php echo e($user->image_folder); ?>"/>
        </div>
        <div class="user_Profile-info__desc">
            <p class="motto">Motto: <?php echo $user->motto; ?></p>
        </div>
    </div>
    <a class="user_Profile-update" href="index.php?<?php echo http_build_query(['route' => 'client', 'pages' => 'edit/profile', 'nickname' => $user->nickname]); ?>">
        <button>Update profile</button>
    </a>
    <a class="user_Profile-update" href="index.php?<?php echo http_build_query(['route' => 'admin', 'pages' => 'delete/user', 'nickname' => $user->nickname]); ?>">
        <button>Delete profile</button>
    </a>
</div>

<div class="content-layout-profile">

    <div class="masonry-container-profile">
        <?php $image_location = include __DIR__ . '/../../../src/Variables/image_location.php'; ?>
        <?php foreach ($entries as $entry): ?>
            <div class="masonry-item" <?php if($entry['deleted'] == 1): ?> style="background-color:red" <?php elseif($entry['status'] == 'private') : ?> style="background-color:green" <?php endif; ?>>
                <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'post', 'post_id' => $entry['posts_id']]); ?>">
                    <img class="item_image" src="<?php echo $image_location; ?><?php echo e($entry['image_folder']); ?>" alt="Post image">
                </a>
                <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'post', 'post_id' => $entry['posts_id']]); ?>">
                    <h4 class="post-title"><?php echo e($entry['title']); ?></h4>
                </a>
                <p class="post-description"><?php echo e($entry['content']); ?></p>
                <div class="post-categories">
                    <?php $categories = explode(', ', $entry['categories']); ?>
                    <?php foreach ($categories as $i => $cat_name): ?>
                        <a class="category-badge" href="index.php?<?php echo http_build_query(['route' => 'client', 'pages' => 'category', 'category' => $cat_name, 'page' => 1]); ?>">
                            <?php echo e($cat_name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
</div>

<ul class="pagination">
<li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname' => $user->nickname, 'page' => 1]); ?>">&laquo;&laquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page === 1) : echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname'=> $user->nickname, 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname'=> $user->nickname, 'page' => ($page-1)]); endif; ?>">&laquo;
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
            href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname'=> $user->nickname, 'page' => $pag]); ?>">
            <?php echo e($pag); ?>
        </a>
    </li>
    <?php endforeach; ?>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page == $num_pages) : echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname'=> $user->nickname, 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname'=> $user->nickname, 'page' => ($page+1)]); endif; ?>">&raquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname'=> $user->nickname, 'page' => $num_pages]); ?>">&raquo;&raquo;</a>
    </li>
</ul>

