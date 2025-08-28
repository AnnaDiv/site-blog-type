<table>
    <thead>
        <th>ID</th>
        <th>Nickname</th>
        <th>Action</th>
    </thead>
    <tbody>
        <?php foreach ($users AS $user) : ?>
            <tr>
                <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'edit/user', 'nickname' => $user['nickname']]); ?>">
                    <td><?php echo $user['users_id']; ?></td>
                </a>
                <td><?php echo $user['nickname']; ?></td>
                <td>
                    <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'view/user', 'nickname' => $user['nickname']]); ?>"><button>View</button></a>
                    <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'edit/user', 'nickname' => $user['nickname']]); ?>"><button>Edit</button></a>
                    <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'delete/user', 'nickname' => $user['nickname']]); ?>"><button>Delete</button></a>
                </td>
            <tr>
        <?php endforeach; ?>
    </tbody>
</table>