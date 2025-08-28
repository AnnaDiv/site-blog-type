<p>Category Title: <?php echo $category->title; ?>
<br>
Category Description: <?php echo $category->description; ?>
<p>


<a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'edit/category', 'category' => $_GET['category']]); ?>"><button>Edit</button></a>
<a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'delete/category', 'category' => $_GET['category']]); ?>"><button>Delete</button></a>