<form method="POST" enctype="multipart/form-data" action="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'edit/profile', 'nickname' => $user->nickname]); ?>">
    
    <input type="hidden" name="_csrf" value="<?php echo e(csrf_token()); ?>"/>

    <div class="profile-up">
        <div class="profile-image-up">
            <?php $image_location = include __DIR__ . '/../../../src/Variables/image_location.php'; ?>
            <img id="post_image" class="profile-image" src="<?php echo $image_location; ?><?php echo e($user->image_folder); ?>"/>
            <label for="image">Image</label>
            <input type="file" name="image" id="image"/>
        </div>
        <div class="profile-info-up">
            <input type="hidden" name="prof_id" id="prof_id" value="<?php echo e($user->users_id); ?>"/>

            <label for="nickname">Nickname</label>
            <input type="text" name="nickname" id="nickname" value="<?php if (isset($_POST['nickname'])) echo e($_POST['nickname']); else echo e($user->nickname); ?>"/>

            <label for="motto">Motto</label> 
            <input type="text" name="motto" id="motto" value="<?php if (isset($_POST['motto'])) echo e($_POST['motto']); else echo e($user->motto); ?>"/>

            <label for="email">Email</label>
            <input type="text" name="email" id="email" value="<?php if (isset($_POST['email'])) echo e($_POST['email']); else echo e($user->email); ?>"/>

            <div>Change Password:
                <label for="old_pass">Old Password</label>
                <input type="password" name="old_pass" id="old_pass"/>
            <br>
                <label for="new_pass">New Password</label>
                <input type="password" name="new_pass" id="new_pass"/>

                <label>
                    <input type="checkbox" onclick="showPass()"> Show Passwords
                </label>
            </div>
        </div>
    </div>

    <input type="submit" name="submit" value="Update"/>
</form>
<?php foreach ($errors AS $error) :?>
    <p><?php echo $error; ?>
<?php endforeach; ?>
<script>
function showPass() {
  var old = document.getElementById("old_pass");
  var newp = document.getElementById("new_pass");
  old.type = old.type === "password" ? "text" : "password";
  newp.type = newp.type === "password" ? "text" : "password";
}
const userID = <?php echo (int) $user->users_id; ?>;
</script>
<script src="./js/imageCreator.js"></script>