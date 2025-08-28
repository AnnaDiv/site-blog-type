function fetchFollows() {
  fetch(
    'src/APIs/follow.api.php?action=getFollow&profileUser=' +
      profileUserNickname +
      '&follower=' +
      currentUserNickname
  )
    .then(res => res.text())
    .then(text => {
      console.log('GET raw response:', text); // 👀 See full PHP output

      try {
        const data = JSON.parse(text);
        console.log('GET parsed JSON:', data);

        if (!Array.isArray(data) || data.length < 2) {
          console.error('Unexpected data format:', data);
          return;
        }

        userFollows = parseInt(data[0]);
        const totalFollows = data[1];

        document.getElementById('follow-count').textContent =
          `${totalFollows} follows`;

        const FollowImg = document.querySelector('#follow-toggle img');
        FollowImg.src =
          userFollows === 1
            ? './content/post/follow.png'
            : './content/post/not_follow.png';
      } catch (err) {
        console.error('JSON parse failed! Raw response was:', text);
      }
    });
}

if (FollowToggleButton) {
  FollowToggleButton.addEventListener('click', function (e) {
    e.preventDefault();

    fetch('src/APIs/follow.api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'follow',
        follower: currentUserNickname,
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
            fetchFollows();
          } else {
            alert(data.error || 'Failed to toggle follow');
          }
        } catch (err) {
          console.error('JSON parse failed! Raw response was:', text);
        }
      });
  });
}

fetchFollows();