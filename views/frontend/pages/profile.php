<script src="./js/dropdown.js"></script>

<div class="user_Profile">
    <div class="user_Profile-info">
        <div class="user_Profile-info__name">
            <h2 class="username">User: <?php echo $user->nickname; ?></h2>
            <?php $image_location = include __DIR__ . '/../../../src/Variables/image_location.php'; ?>
            <img class="user_Profile-info__image" src="<?php echo $image_location; ?><?php echo e($user->image_folder); ?>"/>
        </div>
        <div class="user_Profile-info__desc">
            <p class="motto">Motto: <?php echo $user->motto; ?></p>
        </div>
    </div>
    <?php if ($isprofowner === true) : ?>
        <a class="user_Profile-update" href="index.php?<?php echo http_build_query(['route' => 'client', 'pages' => 'edit/profile', 'nickname' => $user->nickname]); ?>">
            <button>Update your profile</button>
        </a>
    <?php endif; ?>
    <?php if (($status === true) && ($isprofowner === false)) : ?>
        <div class="follow-wrapper">
            <div id="followers_text"> Followers: </div><span id="follow-count">0</span> <br><br><br><br>
            <button id="follow-toggle">
              <img class="like-button" src="./content/post/not_follow.png" alt="follow-image" />
            </button>
        </div>
    <?php endif; ?>
    <?php if (($status === true) && ($isprofowner === false) && ($user->nickname !== 'Anna')) : ?>
        <div class="follow-wrapper">
            <button id="block-toggle">
              <img class="like-button" src="./content/post/unblocked.png" alt="block-image" />
            </button>
        </div>
    <?php endif; ?>
</div>

<div class="content-layout-profile">

    <div class="profile-status-switch">    
        <?php if ($isprofowner === true) : ?>
            <div class="dropdown-div">
                <div>Current Pofile view: <?php if ($_GET['pages'] === 'profile') echo 'Public'; else echo 'Private'; ?></div>
                <div class="dropdown">
                    <button onclick="dropdown_function()" class="dropbtn">Switch</button>
                    <div id="postDropdown" class="dropdown-content">
                        <a href="index.php?<?php echo http_build_query(['route' => 'client', 'pages' => 'profile', 'nickname' => $user->nickname, 'page' => 1]); ?>">Public Posts</a>
                        <a href="index.php?<?php echo http_build_query(['route' => 'client', 'pages' => 'profile/private', 'nickname' => $user->nickname, 'page' => 1]); ?>">Private Posts</a>
                    </div>
                </div> 
            </div>
        <?php endif; ?>
    </div>

    <div class="masonry-container-profile">
        <?php $image_location = include __DIR__ . '/../../../src/Variables/image_location.php'; ?>
        <?php foreach ($entries as $entry): ?>
            <div class="masonry-item">
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


<ul class="pagination floating-pagination">
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname' => $user->nickname, 'page' => 1]); ?>">&laquo;&laquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page === 1) : echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname'=> $user->nickname, 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname'=> $user->nickname, 'page' => ($page-1)]); endif; ?>">&laquo;
        </a>
    </li>
    <?php $page_shown = show_pages($num_pages, $page); ?>
    <?php foreach($page_shown AS $pag) : ?>
    <?php $page = (int) $_GET['page'] ?? 1; ?>
    <li class="pagination__li">
        <a class="pagination__link<?php if ($page === $pag) echo ' pagination__link--active'; ?>"
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

<script>
const profileUserNickname = <?php echo json_encode($user->nickname); ?>;
const currentUserNickname = <?php echo json_encode($_SESSION['nickname'] ?? ''); ?>;
/*console.log("Using postId:", postId); */
const FollowToggleButton = document.getElementById('follow-toggle');
const FollowCountDisplay = document.getElementById('follow-count');
let isFollowing = 0;

const BlockToggleButton = document.getElementById('block-toggle');
</script>
<script src="./js/follow.js"></script>
<script src="./js/block.js"></script>