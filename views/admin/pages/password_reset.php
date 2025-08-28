<?php  $requestID = $_GET['requestID']; ?>
<div class="login-container">
    <form method="POST" class="login-form" action="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'request_pass', 'requestID' => $requestID ]); ?>">
            
        <input type="hidden" name="_csrf" value="<?php echo e(csrf_token()); ?>"/>

        <input type="hidden" name="token" value="<?php ?>"/>

        <div>Change Password:
            <label for="pass_one">New Password</label>
            <input type="password" name="pass_one" id="pass_one" oninput="setpass()"/>
        <br>
            <label for="pass_two">New Password</label>
            <input type="password" name="pass_two" id="pass_two" oninput="setpass()"/>
            <div id="fail_p">Not the same password</div>

            <div class="form-options">
                <label>
                    <input type="checkbox" onclick="showPass()"> Show Passwords
                </label>
            </div>
        </div>

        <input type="submit" name="submit" value="Submit"/>
    </form>
</div>

<script>
function showPass() {
  var old = document.getElementById("pass_one");
  var newp = document.getElementById("pass_two");
  old.type = old.type === "password" ? "text" : "password";
  newp.type = newp.type === "password" ? "text" : "password";
}

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
</script>