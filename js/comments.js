document.addEventListener('DOMContentLoaded', () => {

  function fetchComments() {
    fetch('src/APIs/comments.api.php?action=fetch&post_id=' + postId)
      .then(res => res.json())
      .then(data => {
        list.innerHTML = '';
        if (data.length === 0) {
          list.innerHTML = '<p>No comments yet.</p>';
          return;
        }

        data.forEach(comment => {
          const div = document.createElement('div');
          div.className = 'comment';
          div.id = 'comment' + comment.comments_id; 

          div.innerHTML = `<div class="comment-body">
              <a href="index.php?route=client&pages=profile&nickname=${comment.users_nickname}&page=1"><strong>${comment.users_nickname}</strong></a>: ${comment.contentComment}
              <br><small>${new Date(comment.time).toLocaleString()}</small>
            </div>`;

          if (comment.users_nickname === userNickname || isAdmin === 'y') {
            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'delete-comment';
            deleteBtn.dataset.id = comment.comments_id;
            deleteBtn.innerHTML = '🗑️';
            div.appendChild(deleteBtn);
          }

          list.appendChild(div);
        });

        // Bind delete buttons
        document.querySelectorAll('.delete-comment').forEach(btn => {
          btn.addEventListener('click', function () {
            const commentId = this.dataset.id;
            fetch('src/APIs/comments.api.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: new URLSearchParams({
                action: 'remove',
                comments_id: commentId,
                post_id: postId
              })
            })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  fetchComments();
                } else {
                  alert(data.error || 'Failed to delete comment');
                }
              });
          });
        });
      });
  }

  if (form && input && list) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      fetch('src/APIs/comments.api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'add',
          comment: input.value,
          post_id: postId,
          post_owner: postOwner
        })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            input.value = '';
            fetchComments(); // Refresh comments
          } else {
            alert(data.error || 'Failed to post comment');
          }
        });
    });

    fetchComments(); // Initial load
  } else {
    console.warn('Comments: Missing form, input, or list element');
  }
});
