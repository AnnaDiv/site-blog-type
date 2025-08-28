<div class="login-container">
    <form class="login-form" method="POST" action="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'login']); ?>">
        <input type="hidden" name="_csrf" value="<?php echo e(csrf_token()); ?>"/>

        <h2>Login to Your Account</h2>

        <label for="email">Email</label>
        <input type="text" name="email" id="email" placeholder="you@example.com" required />

        <label for="pass">Password</label>
        <input type="password" name="pass" id="pass" placeholder="••••••••" required />

        <div class="form-options">
            <label>
                <input type="checkbox" onclick="showPass()"> Show Password
            </label>
        </div>

        <input class="login-btn" type="submit" name="submit" value="Login"/>
    </form>

    <div class="login-footer">
    <p>Don’t have an account?
        <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'create/login']); ?>">
            <button class="secondary-btn">Create Account</button>
        </a>
    </p>
    <p>Need help?
        <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'help']); ?>">
            <button class="secondary-btn">Account Help</button>
        </a>
    </p>
    </div>
</div>


<?php if (!empty($loginerror)) var_dump($loginerror); ?>
<script>
function showPass() {
  var old = document.getElementById("pass");
  old.type = old.type === "password" ? "text" : "password";
}
</script>