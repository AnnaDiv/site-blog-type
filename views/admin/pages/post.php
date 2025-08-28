<div class="post-container">
  <div class="post-layout-flex">
    
    <!-- LEFT: image -->
    <div class="post-image">
        <?php $image_location = include __DIR__ . '/../../../src/Variables/image_location.php'; ?>
        <img src="<?php echo $image_location; ?><?php echo e($post['image_folder']); ?>" alt="Post image" />
        <div class="post-actions">
            <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'edit/post', 'post_id' => $post['posts_id']]); ?>"><button>Edit</button></a>
            <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'delete/post', 'post_id' => $post['posts_id']]); ?>"><button>Delete(put it in delete bin)</button></a>
            <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'perma/delete/post', 'post_id' => $post['posts_id']]); ?>"><button>Delete(perma delete)</button></a>
            <?php if($post['deleted'] == 1): ?>
              <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'reinstate/post', 'post_id' => $post['posts_id']]); ?>"><button>Reinstate</button></a>
            <?php endif; ?>
        </div>
    </div>

    <!-- RIGHT: title + description + categories + comments -->
    <div class="post-content">
      <h2 class="post-title"><?php echo e($post['title']); ?></h2>
      <p class="post-description"><?php echo nl2br(e($post['content'])); ?></p>
      <p class="post-description">Post Owner: <a href="index.php?<?php echo http_build_query(['route' => 'client', 'pages' => 'profile', 'nickname' => $post['user_nickname'] , 'page' => 1]); ?>"><?php echo $post['user_nickname']; ?></a>
      </p>
      <div class="post-categories">Categories:
        <?php $comma = 0; $categories = explode(',', $post['categories']); ?>
        <?php foreach ($categories as $i => $cat_name): ?>
            <?php if ($cat_name !== 'none') : ?>
                <a class="category-badge" href="index.php?<?php echo http_build_query(['route' => 'client', 'pages' => 'category', 'category' => trim($cat_name), 'page' => 1]); ?>">
                    <?php echo e($cat_name); ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <!-- Comments section below content -->
      <div class="comments-section">
        <h3>Comments</h3>

        <div class="comment">
          <div class="comment-author">Jane Doe</div>
          <div class="comment-text">Great post! Really enjoyed reading it.</div>
        </div>

        <div class="comment">
          <div class="comment-author">John Smith</div>
          <div class="comment-text">Thanks for sharing your thoughts.</div>
        </div>

        <form class="add-comment" method="POST" action="">
          <label for="name">Name</label>
          <input type="text" id="name" name="name" required>

          <label for="comment">Comment</label>
          <textarea id="comment" name="comment" rows="4" required></textarea>

          <button type="submit">Post Comment</button>
        </form>
      </div>
    </div>

  </div>
</div>
