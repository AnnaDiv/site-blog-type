<form method="POST" action="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'create/category']); ?>">
    
    <input type="hidden" name="_csrf" value="<?php echo e(csrf_token()); ?>"/>

    <label for="title">Category Title</label>
    <input type="text" name="title" id="title" value="<?php if (isset($_POST['title'])) echo e($_POST['title']); ?>"/>

    <label for="description">Category Description</label> 
    <textarea name="description" id="description"><?php if (isset($_POST['description'])) echo e($_POST['description']); ?></textarea>

    <input type="submit" name="submit" value="Create"/>
</form>