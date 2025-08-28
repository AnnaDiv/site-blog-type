<div>
    Delete User:<br>
    User ID: <?php echo $user->users_id; ?><br>
    User Nickname: <?php echo $user->nickname; ?><br>
    User Email: <?php echo $user->email; ?><br>
<div>
    <br>
<form method="POST" action="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'delete/user', 'nickname' => $nickname ]); ?>">
        
    <input type="hidden" name="_csrf" value="<?php echo e(csrf_token()); ?>"/>

    <input type="submit" name="submit" value="Delete"/>
</form>
