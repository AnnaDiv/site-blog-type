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

    <?php $image_location = include __DIR__ . '/../../../src/Variables/image_location.php'; ?>

    <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'myart', 'page' => 1]); ?>">
        <div class="banner">
            <div class="banner-title">My art</div>
            <?php foreach($art_images as $index => $art_image) : ?>
                <img class="art_image <?php echo $index === 0 ? 'active' : ''; ?>" 
                    src="<?php echo $image_location; ?><?php echo $art_image['image_folder']; ?>" 
                    alt="Post image">
            <?php endforeach; ?>
            <div class="banner-desc">^^^^<br>Check my art page out</div>
        </div>
    </a>

    <div class="masonry-wrapper">
        <div class="masonry-container">
            <?php foreach ($entries as $entry): ?>
                <div class="masonry-item">
                    <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'post', 'post_id' => $entry['posts_id']]); ?>">
                        <img class="item_image" src="<?php echo $image_location; ?><?php echo e($entry['image_folder']); ?>" alt="Post image">
                    </a>
                    <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'post', 'post_id' => $entry['posts_id']]); ?>">
                        <h4 class="post-title"><?php echo e(ucfirst($entry['title'])); ?></h4>
                    </a>
                    <p class="post-description"><?php echo e(ucfirst($entry['content'])); ?></p>
                    <div class="post-categories">
                        <?php $categories = explode(', ', $entry['categories']); ?>
                        <?php foreach ($categories as $i => $cat_name): ?>
                            <?php if ($cat_name !== 'none') : ?>
                                <a class="category-badge" href="index.php?<?php echo http_build_query(['route' => 'client', 'pages' => 'category', 'category' => $cat_name, 'page' => 1]); ?>">
                                    <?php echo e($cat_name); ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="post-categories2">
                        <div class="post-owner">
                            By: <a href="index.php?<?php echo http_build_query(['route' => 'client', 'pages' => 'profile', 'nickname' => $entry['user_nickname'] ,'page' => 1]); ?>">
                            <?php echo e($entry['user_nickname']); ?>
                            </a>
                        </div>
                        <div class="hover-wrapper">
                            <img class="dots" src="./content/post/3dots_.png" alt="Options">
                            <div class="hover-info">
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

<?php $page = (int) $_GET['page'] ?? 1; ?>

<ul class="pagination floating-pagination">
<li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'main', 'page' => 1]); ?>">&laquo;&laquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page === 1) : echo http_build_query(['route' => 'client' , 'pages' => 'main', 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'main', 'page' => ($page-1)]); endif; ?>">&laquo;
        </a>
    </li>
    <?php $page_shown = show_pages($num_pages, $page); ?>
    <?php foreach($page_shown AS $pag) : ?>
    <li class="pagination__li">
        <a class="pagination__link<?php if ($page === $pag) echo ' pagination__link--active'; ?>"
            href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'main', 'page' => $pag]); ?>">
            <?php echo e($pag); ?>
        </a>
    </li>
    <?php endforeach; ?>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php if ($page == $num_pages) : echo http_build_query(['route' => 'client' , 'pages' => 'main', 'page' => $page]); else : echo http_build_query(['route' => 'client' , 'pages' => 'main', 'page' => ($page+1)]); endif; ?>">&raquo;</a>
    </li>
    <li class="pagination__li">
        <a class="pagination__link pagination_link_ends" href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'main', 'page' => $num_pages]); ?>">&raquo;&raquo;</a>
    </li>
</ul>

<script>
  const form = document.getElementById('searchForm');
  const input = document.getElementById('search_q');
  const images = document.querySelectorAll('.banner .art_image');
  let current = 0;

  function showNextImage() {
    images[current].classList.remove('active');
    current = (current + 1) % images.length;
    images[current].classList.add('active');
  }

  setInterval(showNextImage, 3000); // rotate banner every 3 seconds
</script>
<script src="./js/search_selector.js"></script>