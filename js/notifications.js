document.addEventListener('DOMContentLoaded', function () {
    const dropdown = document.getElementById('dropdownList');
    const toggleBtn = document.getElementById('dropdownToggle');

    if (!dropdown || !toggleBtn) return;

    // Toggle dropdown visibility
    toggleBtn.addEventListener('click', () => {
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    // Fetch notifications
    fetch('src/APIs/notifications.api.php?action=list')
        .then(res => res.json())
        .then(data => {
            dropdown.innerHTML = ''; // Clear existing list

            if (!Array.isArray(data) || data.length === 0) {
                dropdown.innerHTML = '<li>No notifications</li>';
                toggleBtn.classList.remove('has-unread');
                return;
            }

            let hasUnread = false;

            data.forEach(item => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                li.className = 'notification';
                a.href = item.link;
                a.textContent = item.content;

                // Style as read/unread
                if (item.used == 1) {
                    li.classList.add('notification-read');
                    a.classList.add('read-link');
                } else {
                    li.classList.add('notification-unread');
                    a.classList.add('unread-link');
                    hasUnread = true;
                }

                // On click, mark as read then redirect
                a.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetUrl = item.link;

                    fetch('src/APIs/notifications.api.php?action=mark_read', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ notification_id: item.notification_id })
                    })
                        .then(res => res.json())
                        .then(() => {
                            window.location.href = targetUrl;
                        })
                        .catch(err => {
                            console.error('Failed to mark as read:', err);
                            window.location.href = targetUrl;
                        });
                });

                li.appendChild(a);
                dropdown.appendChild(li);
            });

            // Add class if there's any unread
            if (hasUnread) {
                toggleBtn.classList.add('has-unread');
            } else {
                toggleBtn.classList.remove('has-unread');
            }
        })
        .catch(err => {
            dropdown.innerHTML = '<li>Error loading notifications</li>';
            console.error(err);
        });
});