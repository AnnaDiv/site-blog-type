<div class="simple-text">
    <h2> Choose something you want to have an overwiew on: </h2>

    <ol>
        <li><a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'edit/categories']); ?>">Categories</a></li>
        <li><a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'edit/users']); ?>">Users</a></li>
        <li><a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'deleted/posts']); ?>">Deleted Posts</a></li>
    </ol>
</div>