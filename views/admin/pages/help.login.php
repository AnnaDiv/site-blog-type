<style>
    .login-container{
        max-width: 40% !important;
        min-width: 20% !important;
    }
    .login-form{
        display: flex;
    }
</style>
<div class="login-container">
    <h3>Provide your email to get a verification link to change your password:</h3>
    <form method="POST" class="login-form" action="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'help/login']); ?>">
        
        <input type="hidden" name="_csrf" value="<?php echo e(csrf_token()); ?>"/>

        <label for="email">Email</label>
        <input type="text" name="email" id="email" value="<?php if (isset($_POST['email'])) echo e($_POST['email']); ?>" placeholder="you@example.com"/>

        <input type="submit" name="submit" value="Send Email"/>
    </form>
</div>