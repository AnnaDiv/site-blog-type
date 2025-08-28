<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./styles/normalize.css" />
    <link rel="stylesheet" type="text/css" href="./styles/nav_n_top.css" />
    <link rel="stylesheet" type="text/css" href="./styles/main.css" />
    <link rel="stylesheet" type="text/css" href="./styles/pagination.css" />
    <link rel="stylesheet" type="text/css" href="./styles/post.css" />
    <link rel="stylesheet" type="text/css" href="./styles/profile.css" />
    <link rel="stylesheet" type="text/css" href="./styles/dropdown.css" />
    <link rel="stylesheet" type="text/css" href="./styles/create_edit_post.css" />
    <link rel="stylesheet" type="text/css" href="./styles/simple-text.css" />
    <link rel="stylesheet" type="text/css" href="./styles/login-form.css" />
    <link rel="stylesheet" type="text/css" href="./styles/category-list.css" />
    <link rel="stylesheet" type="text/css" href="./styles/my_art.css" />
    <script src="./js/autocomplete.js"></script>
    <script src="./js/nav.js"></script>
    <title>Love it OR Throw it</title>
</head>
<header>
    <h1 class="site-title"> Love it OR Throw it </h1>
    <div class="user">
        <div id="user-status" class="user-status">
            <a href="index.php?<?php echo http_build_query(['route' => 'admin', 'pages' => 'logout']); ?>">Logout</a>
        </div>
        <div class="profile-pic">
            <img id="profile-toggle" src="<?php echo $_SESSION['profile_pic']; ?>" alt="Profile Picture">
            <nav id="side-nav-profile">
                <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname' => $_SESSION['nickname'], 'page' => 1]); ?>">My profile</a>
                <a href="index.php?<?php echo http_build_query(['route' => 'admin', 'pages' => 'logout']); ?>">Logout</a>
            </nav>
        </div>
    </div>
</header>
<body>
   <!-- Inside <body> -->
<div class="container">
    <!-- Hamburger menu button -->
    <button id="menu-toggle" class="hamburger">&#9776;</button>

    <!-- Sidebar nav -->
    <nav id="side-nav">
        <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'browse', 'page' => 1]); ?>">Browse</a>
        <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'myart', 'page' => 1]); ?>">My art</a>
        <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'categories', 'page' => 1 ]); ?>">Categories</a>
        <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'contact_us']); ?>">Contact us</a>
        <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'control']); ?>">Control Panel</a>
        <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'create']); ?>">Create Post</a>
        <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname' => $_SESSION['nickname'], 'page' => 1]); ?>">My profile</a>
    </nav>

    <!-- Main post area -->
    <main>
        <?php echo $contents; ?>
    </main>
</div>
</body>
</html>