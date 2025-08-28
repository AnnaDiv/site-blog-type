<form method="POST" action="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'edit/category', 'category' => $category->title]); ?>">
    
    <input type="hidden" name="_csrf" value="<?php echo e(csrf_token()); ?>"/>

    <input type="hidden" name="cat_id" id="cat_id" value="<?php echo e($category->category_id); ?>"/>

    <label for="title">Category Title</label>
    <input type="text" name="title" id="title" value="<?php if (isset($_POST['title'])) echo e($_POST['title']); else echo e($category->title); ?>"/>

    <label for="description">Category Description</label> 
    <textarea name="description" id="description"><?php if (isset($_POST['description'])) echo e($_POST['description']); else echo e($category->description); ?></textarea>

    <input type="submit" name="submit" value="Update"/>
</form>

<?php foreach ($error AS $er) {
    echo $er;
}
?>
