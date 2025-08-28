<?php $entry = $params['entry']; ?>
<div id="edit-post-title"> Lets edit our post </div>

<form method="POST" autocomplete="off" enctype="multipart/form-data" action="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'edit/post', 'post_id' => $entry['posts_id']]); ?>">
    
    <input type="hidden" name="_csrf" value="<?php echo e(csrf_token()); ?>"/>

    <div class="post-container-create">
        <div class="post-layout-flex-create">

            <!-- LEFT: image -->
            <div class="post-image-create">
                <div class="image-preview-wrapper">
                    <img id="post_image" src="./<?php echo e($entry['image_folder']); ?>">
                    <input type="file" name="image" id="image" required>

                    <!-- Spinner -->
                    <div class="image-spinner" id="image-spinner">
                        <div class="spinner"></div>
                    </div>

                    <!-- Progress bar -->
                    <div id="upload-progress-bar"><div class="bar"></div></div>
                </div>
            </div>
            <!-- RIGHT: title + description -->
            <div class="post-content-create">
                <div class="post-form-group">
                    <h2 class="post-title-create">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" value="<?php if (isset($_POST['title'])) echo e($_POST['title']); else echo e($entry['title']); ?>" required/>
                    </h2>

                    <div class="post-description-create">
                        <label for="description" class="description-label">Description</label> 
                        <textarea name="description" id="description" required><?php if (isset($_POST['description'])) echo e($_POST['description']); else echo e($entry['content']); ?></textarea>
                    </div>

                    <?php
                        $post_t = $_POST['post_status'] ?? $entry['status'] ?? 'private';
                        $post_t = ($post_t === 'public') ? 'public' : 'private';
                    ?>

                    <div class="post-visibility-create">
                        <label for="post_status">Post visibility:</label>
                        <select id="post_status" name="post_status">
                            <option value="public" <?= $post_t === 'public' ? 'selected' : '' ?>>Public</option>
                            <option value="private" <?= $post_t === 'private' ? 'selected' : '' ?>>Private</option>
                        </select>
                    </div>

                    <div class="post-categories-create">
                        <label for="categories">Categories</label>
                        <ul id="cats" style="list-style: none; padding: 0; display: flex; flex-wrap: wrap; gap: 8px;"></ul>
                        <input type="hidden" name="categories" id="categories" />
                    </div>

                    <div class="post-categories-create">
                        <label for="category">Enter Category</label>
                        <div style="display:flex" class="autocomplete" style="width:300px;">
                            <input type="text" name="category" id="category"><button onclick="transport_value(event)">Enter</button>
                        </div>
                    </div>

                    <input type="submit" name="submit" value="Update"/>
                </div>

            </div>

        </div>
    </div>

</form>

<?php foreach ($errors AS $error) {
    echo $error;
}
?>
<script>
const userID = <?php echo (int) $_SESSION['usersID']; ?>;
const spinner = document.getElementById('image-spinner');
const progressBarWrapper = document.getElementById('upload-progress-bar');
const progressBar = progressBarWrapper.querySelector('.bar');
const imageInput = document.getElementById('image');
const previewImage = document.querySelector('#post_image');
</script>
<script src="./js/imageCreator.js" defer></script>
<script src="./js/categories.js"></script>
<script>
<?php $category_titles = array_column($params['categories'] ?? [], 'category_title'); ?>
var categories_values = <?php echo json_encode($category_titles); ?>;
display_values();

var category = [];
<?php foreach ($allcategories AS $allcategory) : ?>
    category.push("<?php echo $allcategory['title']?>");
<?php endforeach; ?>
/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
autocomplete(document.getElementById("category"), category);
</script>