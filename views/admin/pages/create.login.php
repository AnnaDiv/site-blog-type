<div class="login-container">
    <form method="POST" class="login-form" action="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'create/login']); ?>">
        
        <input type="hidden" name="_csrf" value="<?php echo e(csrf_token()); ?>"/>

        <label for="email">Email*</label>
        <input type="text" name="email" id="email" value="<?php if (isset($_POST['email'])) echo e($_POST['email']); ?>"/>

        <label for="nickname">Username*</label>
        <input type="text" name="nickname" id="nickname" value="<?php if (isset($_POST['nickname'])) echo e($_POST['nickname']); ?>"/>

        <label for="motto">Your motto</label>
        <input type="text" name="motto" id="motto" value="<?php if (isset($_POST['motto'])) echo e($_POST['motto']); ?>"/>

        <label for="pass_one">Password*</label>
        <input type="password" name="pass_one" id="pass_one"/>
            <label for="pass_two">Repeat Password*</label>
        <input type="password" name="pass_two" id="pass_two" onfocusout="setpass()"/>
        <div id="fail_p">Not the same password</div>
        
        <div class="form-options">
            <label>
                <input type="checkbox" onclick="showPass()"> Show Passwords
            </label>
        </div>
        <br>
    * : need to be filled
        <input type="submit" name="submit" value="Create"/>
    </form>

</div>

<?php if(!empty($errors)) var_dump($errors); ?>
<style>
#fail_p{
    visibility: hidden;
}
</style>
<script>
function setpass(){
    let pass1=document.getElementById("pass_one").value;
    let pass2=document.getElementById("pass_two").value;
    if (pass1==pass2){
        document.getElementById("fail_p").style.visibility="hidden";
    }
    else{
        document.getElementById("pass_two").value = "";
        document.getElementById("fail_p").style.visibility="visible";
    }
}

function showPass() {
  var one = document.getElementById("pass_one");
  var two = document.getElementById("pass_two");
  one.type = one.type === "password" ? "text" : "password";
  two.type = two.type === "password" ? "text" : "password";
}
</script>