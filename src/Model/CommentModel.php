<?php

namespace App\Model;

class CommentModel {

    public int $comments_id;
    public int $posts_id;
    public string $users_nickname;
    public string $contentComment;
    public string $time;

}