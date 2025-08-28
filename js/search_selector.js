document.addEventListener('DOMContentLoaded', function () {

    let clickedButtonTarget = null;

    // Track which button was clicked
    document.querySelectorAll('#searchForm button[type="submit"]').forEach(btn => {
        btn.addEventListener('click', function () {
            clickedButtonTarget = this.getAttribute('data-target'); // "posts" or "users"
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Stop default form action

        const query = encodeURIComponent(input.value.trim());

        if (!query || !clickedButtonTarget) {
            return; // prevent empty queries or unclicked button
        }

        let targetURL = '';

        if (clickedButtonTarget === 'posts') {
            targetURL = `index.php?route=client&pages=search&search_q=${query}&page=1`;
        } else if (clickedButtonTarget === 'users') {
            targetURL = `index.php?route=client&pages=search_user&search_q=${query}&page=1`;
        }

        window.location.href = targetURL;
    });
});