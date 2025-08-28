<?php

require __DIR__ . '/inc/all.inc.php';

//binds
require __DIR__ . '/inc/bind.inc.php';

$route = @(string) ($_GET['route'] ?? 'client');
$pages = @(string) ($_GET['pages'] ?? 'main');

//authservice authentications
$authService = $container->get('authService');
$status = $authService->isLoggedIn();
$isadmin = $authService->isAdmin();

//crsf tokens or not depending the api use
if ($route !== 'api') {
    $csrfHelper = $container->get('csrfHelper');
    $csrfHelper->handle();
}

function csrf_token(){
    global $container;
    $csrfHelper = $container->get('csrfHelper');
    return $csrfHelper->generateToken();
}

if ($route === 'client'){
    
    require __DIR__ . '/inc/routes/client.inc.php';

}
else if ($route === 'admin') {

    require __DIR__ . '/inc/routes/admin.inc.php';

}
elseif ($route === 'api'){

    require __DIR__ . '/inc/routes/api.inc.php';

}
else {
    $notFoundController = $container->get('notFoundController');
    $notFoundController->error404();
}