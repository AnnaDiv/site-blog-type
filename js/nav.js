document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.getElementById('menu-toggle');
    const nav = document.getElementById('side-nav');

    menuToggle.addEventListener('click', function () {
        nav.classList.toggle('open');
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.getElementById('profile-toggle');
    const nav = document.getElementById('side-nav-profile');

    menuToggle.addEventListener('click', function () {
        nav.classList.toggle('open');
    });
});