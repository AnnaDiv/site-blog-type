<div class="post-container-single">
  <div class="post-layout-flex-single">
    
    <!-- LEFT: image -->
    <div class="post-image-single">
        <?php $image_location = include __DIR__ . '/../../../src/Variables/image_location.php'; ?>
        <img src="<?php echo $image_location; ?><?php echo e($post['image_folder']); ?>" alt="Post image" />
        <div class="post-actions-single">
          <div>
            <?php if($isowner === true) : ?>
              <div class="action-buttons-single">
                  <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'edit/post', 'post_id' => $post['posts_id']]); ?>"><button>Edit</button></a>
                  <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'delete/post', 'post_id' => $post['posts_id']]); ?>"><button>Delete</button></a>
              </div>
            <?php endif; ?>
            <?php if($isadmin === true) : ?>
              <div class="action-buttons-single">
                  <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'edit/post', 'post_id' => $post['posts_id']]); ?>"><button>Edit</button></a>
                  <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'delete/post', 'post_id' => $post['posts_id']]); ?>"><button>Delete(put it in delete bin)</button></a>
                  <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'perma_delete/post', 'post_id' => $post['posts_id']]); ?>"><button>Delete(perma delete)</button></a>
                  <?php if($post['deleted'] == 1): ?>
                    <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'reinstate/post', 'post_id' => $post['posts_id']]); ?>"><button>Reinstate</button></a>
                  <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="like-wrapper">
            <span id="like-count">0</span>
            <button id="like-toggle">
              <img class="like-button" src="./content/post/nolikeheart.png" alt="like-image" />
            </button>
          </div>
        </div>
    </div>

    <!-- RIGHT: title + description + categories + comments -->
    <div class="post-content-single">
      
    <h2 class="post-title-single"><?php echo e(ucfirst($post['title'])); ?></h2>
      <p class="post-description-single"><?php echo ucfirst(nl2br(e($post['content']))); ?></p>
      <p class="post-description-single">Post Owner: <a href="index.php?<?php echo http_build_query(['route' => 'client', 'pages' => 'profile', 'nickname' => $post['user_nickname'] , 'page' => 1]); ?>"><?php echo $post['user_nickname']; ?></a></p>
      
      <div class="post-categories-single">
          <span class="category-label">Categories:</span>
          <div class="category-tags">
          <?php $categories = explode(',', $post['categories']); ?>
          <?php foreach ($categories as $cat_name): ?>
              <?php if (trim($cat_name) !== 'none') : ?>
                  <a class="category-badge-single" href="index.php?<?php echo http_build_query([
                      'route' => 'client',
                      'pages' => 'category',
                      'category' => trim($cat_name),
                      'page' => 1
                  ]); ?>">
                      <?php echo e(trim($cat_name)); ?>
                  </a>
              <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
      
      <!-- Comments section below content -->
      <div id="comments-section">
        <h3>Comments</h3>
        <div id="comments-list"></div>

        <?php if (!empty($_SESSION['nickname'])): ?>
            Comment as: <?php echo $_SESSION['nickname']; ?>
        <form id="comment-form">
            <img id="profile-pic-comments" src="<?php echo $_SESSION['profile_pic']; ?>" alt="Profile Picture">
            <textarea id="comment-input" placeholder="Write a comment..." required></textarea>
            <button type="submit">Post</button>
        </form>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<script>
const postId = <?php echo (int) $post['posts_id']; ?>;
const postOwner =  <?php echo json_encode($post['user_nickname']); ?>;
const userNickname = <?php echo json_encode($_SESSION['nickname'] ?? ''); ?>;
const isAdmin = <?php echo json_encode($_SESSION['admin'] ?? ''); ?>;
const list = document.getElementById('comments-list');
const form = document.getElementById('comment-form');
const input = document.getElementById('comment-input');
const likeToggleButton = document.getElementById('like-toggle');
const likeCountDisplay = document.getElementById('like-count');
let userLiked = 0; 
</script>
<script src="./js/comments.js"></script>
<script src="./js/likes.js"></script>