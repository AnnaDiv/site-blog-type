function fetchLikes() {
  fetch('src/APIs/likes.api.php?action=getLikes&post_id=' + postId)
    .then(res => res.json())
    .then(data => {
      if (!Array.isArray(data) || data.length < 2) {
        console.error('Unexpected data format:', data);
        return;
      }

      userLiked = parseInt(data[0]);  // Ensure it's a number
      const totalLikes = data[1];

      document.getElementById('like-count').textContent = `${totalLikes} likes`;

      const likeImg = document.querySelector('#like-toggle img');
      if (userLiked === 1) {
        likeImg.src = './content/post/likeheart.png';
      } else {
        likeImg.src = './content/post/nolikeheart.png';
      }
    });
}

if (likeToggleButton) {
  likeToggleButton.addEventListener('click', function (e) {
    e.preventDefault();

    fetch('src/APIs/likes.api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'like',
        like: userLiked,   // this is the variable we updated in fetchLikes
        post_id: postId,
        post_owner: postOwner
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        fetchLikes(); // refresh like state
      } else {
        alert(data.error || 'Failed to toggle like');
      }
    });
  });
}

fetchLikes(); // Initial load