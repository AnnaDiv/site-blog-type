<form method="POST" autocomplete="off" enctype="multipart/form-data" action="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'create']); ?>">
    
    <input type="hidden" name="_csrf" value="<?php echo e(csrf_token()); ?>"/>

    <div class="post-container-create">
        <div class="post-layout-flex-create">
    
            <!-- LEFT: image -->
            <div class="post-image-create">                
                <div class="image-preview-wrapper">
                    <img id="post_image" src="./content/post/post_here.jpg" alt="Post Image"/>
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
                    <input type="text" name="title" id="title" value="<?php if (isset($_POST['title'])) echo e($_POST['title']); ?>" required/>
                    </h2>

                    <div class="post-description-create">
                        <label for="description" class="description-label">Description</label>
                        <textarea name="description" id="description" required><?php if (isset($_POST['description'])) echo e($_POST['description']); ?></textarea>
                    </div>

                    <div class="post-visibility-create">
                        <label for="post_status">Post visibility:</label>
                        <select id="post_status" name="post_status">
                            <option value="public" selected>Public</option>
                            <option value="private">Private</option>
                        </select>
                    </div>

                    <?php if($isadmin === true) : ?>
                        <div class="post-type">
                            <label for="post_type">Post type:</label>
                            <select id="post_type" name="post_type">
                                <option value="post" selected>Post</option>
                                <option value="art">Art</option>
                            </select>
                        </div>
                    <?php else : ?>
                        <input type="hidden" name="post_type" value="post" />
                    <?php endif; ?>

                    <div class="post-categories-create">
                        <label for="categories">Categories</label>
                        <ul id="cats"></ul>
                        <input type="hidden" name="categories" id="categories" />
                    </div>

                    <div class="post-categories-create">
                        <label for="category">Enter Category</label>
                        <div class="autocomplete">
                            <input type="text" name="category" id="category">
                            <button onclick="transport_value(event)">Go</button>
                        </div>
                    </div>

                    <input type="submit" name="submit" value="Create Post"/>
                </div>
            </div>
            
        </div>
    </div>

</form>

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
var categories_values = [];
var category = [];

<?php foreach ($categories AS $category) : ?>
    category.push("<?php echo $category['title']?>");
<?php endforeach; ?>

autocomplete(document.getElementById("category"), category);
</script>