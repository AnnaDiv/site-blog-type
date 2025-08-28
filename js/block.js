function fetchBlock() {
  fetch(
    'src/APIs/block.api.php?action=getBlock&profileUser=' +
      profileUserNickname +
      '&blockingUser=' +
      currentUserNickname
  )
    .then(res => res.text())
    .then(text => {
      console.log('GET raw response:', text);

      try {
        const data = JSON.parse(text);
        console.log('GET parsed JSON:', data);

        if (!Array.isArray(data) || data.length < 1) {
          console.error('Unexpected data format:', data);
          return;
        }

        const isBlocked = parseInt(data[0]);

        const BlockImg = document.querySelector('#block-toggle img');
        const FollowImg = document.querySelector('#follow-toggle img');

        BlockImg.src =
          isBlocked === 1
            ? './content/post/blocked.png'
            : './content/post/unblocked.png';
        if (isBlocked === 1) {
            FollowImg.src = './content/post/not_follow.png';
        }
      } catch (err) {
        console.error('JSON parse failed! Raw response was:', text);
      }
    });
}

if (BlockToggleButton) {
  BlockToggleButton.addEventListener('click', function (e) {
    e.preventDefault();

    fetch('src/APIs/block.api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'block',
        blockingUser: currentUserNickname,
        profileUser: profileUserNickname,
      }),
    })
      .then(res => res.text())
      .then(text => {
        console.log('POST raw response:', text);

        try {
          const data = JSON.parse(text);
          console.log('POST parsed JSON:', data);

          if (data.success) {
            fetchBlock();
          } else {
            alert(data.error || 'Failed to toggle block');
          }
        } catch (err) {
          console.error('JSON parse failed! Raw response was:', text);
        }
      });
  });
}

fetchBlock();