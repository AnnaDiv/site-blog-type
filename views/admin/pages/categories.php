<h3>Category Overview</h3> <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'create/category']); ?>"><button>Create New Category</button></a>
<table>
    <thead>
        <th>Categories</th>
        <th>Actions</th>
    </thead> 
<tbody>  
<?php foreach ($categories AS $cat) : ?>
    <?php if ($cat['title'] === 'none') continue; ?>
    <tr>
    <td><?php echo $cat['title']; ?></a></td>
    <td><a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'view/category', 'category' => $cat['title']]); ?>"><button>View</button></a>
        <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'edit/category', 'category' => $cat['title']]); ?>"><button>Edit</button></a>
        <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'delete/category', 'category' => $cat['title']]); ?>"><button>Delete</button></a></td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>