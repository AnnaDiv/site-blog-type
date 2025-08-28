<script src="./js/dropdown.js"></script>

<div class="user_Profile">
    <div class="user_Profile-info">
        <div class="user_Profile-info__name">
            <h2 class="username">User: <?php echo $user_nickname; ?></h2>
            <?php $image_location = include __DIR__ . '/../../../src/Variables/image_location.php'; ?>
            <img class="user_Profile-info__image" src="<?php echo $image_location; ?>content/user/alt/blank.jpeg"/>
        </div>
        <div>
            <span><?php echo $user_nickname; ?> has blocked you, you cannot accesss their profile </span>
        </div>
    </div>
    <?php if (($status === true) && ($isprofowner === false) && ($user_nickname !== 'Anna')) : ?>
        <div class="follow-wrapper">
            <button id="block-toggle">
              <img class="like-button" src="./content/post/unblocked.png" alt="block-image" />
            </button>
        </div>
    <?php endif; ?>
</div>
<script>
const profileUserNickname = <?php echo json_encode($user_nickname); ?>;
const currentUserNickname = <?php echo json_encode($_SESSION['nickname'] ?? ''); ?>;
const BlockToggleButton = document.getElementById('block-toggle');
</script>
<script src="./js/block.js"></script>