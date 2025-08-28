<h3>Deleted Posts</h3>
<table>
    <thead>
        <th>Posts</th>
        <th>Actions</th>
    </thead> 
<tbody>  
<?php foreach ($entries AS $entry) : ?>
    <tr>
    <td>Posts_id: <?php echo $entry['posts_id']; ?>, User: <?php echo $entry['user_nickname']; ?>, Title: <?php echo $entry['title']; ?></td>
    <td><a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'post', 'post_id' => $entry['posts_id']]); ?>"><button>View</button></a>
        <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'reinstate/post', 'post_id' => $entry['posts_id']]); ?>"><button>Reinstate</button></a>
        <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'perma_delete/post', 'post_id' => $entry['posts_id']]); ?>"><button>Perma Delete</button></a></td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>