<div class="content-layout">
    <h1 style="text-align: center;">Welcome!</h1>

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

    <div id="intro">
        <div class="simple-text-intro">
            <h2>Welcome to my site!</h2>
            <p>I made this by myself, pretty cool right?<br>
                <br>I am learning to code by making this site.<br>
                Looking for work and excited to continue learning how to code in depth<br>
                <br>Please check out my site and socials<br>
                <br><strong>Go to my Laravel 12 -based site, it even has working live chat!</strong><br>
                <br><a class="button" href="http://www.laravel.site.gr">zaranna Laravel 12 site</a><br>
            </p>
        </div>
        <div class="simple-text-intro" style="display:flex; justify-content:space-between">
            <div>
                <h2>Find me on linkedin!</h2>
                <p>
                    <a href="https://www.linkedin.com/"><i class="fab fa-linkedin"></i>LinkedIn</a>
                </p>
            </div>
            <div style="margin-right:2rem">
                <h2>Email me!</h2>
                <p>annikoulini001@gmail.com</p>
            </div>
        </div>
    </div>

    <div id="social-info">
        <div class="simple-text-banner">
            <h2>Visit my GitHub!</h2>
            <a href="https://github.com/AnnaDiv"><i class="fab fa-github" aria-hidden="true"></i>AnnaDiv</a>
            <span class="gh-stats">
                <small>🌟 3 repos</small>
            </span>
        </div>
        <div class="simple-text-banner">
            <h2>PHP Vanilla Repo</h2>
            <a href="https://github.com/AnnaDiv/site-blog-type">PHP Vanilla Site</a>
        </div>
        <div class="simple-text-banner">
            <h2>Laravel 12 Repo</h2>
            <a href="https://github.com/AnnaDiv/blog-site-laravel">Laravel 12 Site</a>
        </div>
    </div>

</div>
<script>
  const images = document.querySelectorAll('.banner .art_image');
  let current = 0;

  function showNextImage() {
    images[current].classList.remove('active');
    current = (current + 1) % images.length;
    images[current].classList.add('active');
  }

  setInterval(showNextImage, 3000); // Change every 3 seconds
</script>
<script> 
   const form = document.getElementById('searchForm');
   const input = document.getElementById('search_q');
</script>
<script src="./js/search_selector.js"></script>