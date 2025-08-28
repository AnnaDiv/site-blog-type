<?php

$container = \App\Support\Container::getInstance();

$container->bind('pdo', function(){
    return require __DIR__ . '/db-connect.inc.php'; 
});

$container->bind('authService', function() use($container){
    $pdo = $container->get('pdo');
    return new App\Admin\Support\AuthService($pdo);
});

$container->bind('csrfHelper', function() use($container){
    return new App\Support\CsrfHelper();
});

$container->bind('entriesRepository', function() use($container){
    $pdo = $container->get('pdo');
    return new App\Repository\EntriesRepository($pdo);
});

$container->bind('categoriesRepository', function() use($container){
    $pdo = $container->get('pdo');
    return new App\Repository\CategoriesRepository($pdo);
});

$container->bind('usersRepository', function() use($container){
    $pdo = $container->get('pdo');
    return new App\Repository\UsersRepository($pdo);    
});

$container->bind('entriesController', function() use($container){
    $entriesRepository = $container->get('entriesRepository');
    return new App\FrontEnd\Controller\EntriesController($entriesRepository);
});

$container->bind('categoriesController', function() use($container){
    $categoriesRepository = $container->get('categoriesRepository');
    return new App\FrontEnd\Controller\CategoriesController($categoriesRepository);
});

$container->bind('notFoundController', function() use($container){
    $entriesRepository = $container->get('entriesRepository');
    return new App\FrontEnd\Controller\NotFoundController($entriesRepository);
});

$container->bind('loginController', function() use($container){
    $authService = $container->get('authService');
    return new App\Admin\Controller\LoginController($authService);
});

$container->bind('managerController', function() use($container){
    return new App\Admin\Controller\ManagerController();
});

$container->bind('entriesManagerController', function() use($container){
    $entriesRepository = $container->get('entriesRepository');
    return new App\Admin\Controller\EntriesManagerController($entriesRepository);
});

$container->bind('categoriesManagerController', function() use($container){
    $categoriesRepository = $container->get('categoriesRepository');
    return new App\Admin\Controller\CategoriesManagerController($categoriesRepository);
});

$container->bind('usersManagerController', function() use($container) {
    $usersRepository = $container->get('usersRepository');
    $phpMailerService = $container->get('PHPMailerService');
    return new App\Admin\Controller\UsersManagerController($usersRepository,$phpMailerService);
});

$container->bind('usersController', function() use($container){
    $usersRepository = $container->get('usersRepository');
    return new App\FrontEnd\Controller\UsersController($usersRepository);
});

$container->bind('clientController', function() use($container){
    return new App\FrontEnd\Controller\ClientController;
});

//MAIL SERVER BINDINGS
$container->bind('PHPMailer', function() use($container){
    return new PHPMailer\PHPMailer\PHPMailer(true);
});

$container->bind('PHPMailerService', function() use($container){
    $phpMailer = $container->get('PHPMailer');
    $pdo = $container->get('pdo');
    return new App\Admin\Support\PHPMailerService($phpMailer, $pdo);
});

$container->bind('PHPMailerController', function() use($container){
    $phpMailerService = $container->get('PHPMailerService');
    return new App\Admin\Controller\PHPMailerController($phpMailerService);
});

$container->bind('requestsRepository', function() use($container){
    $pdo = $container->get('pdo');
    return new App\Repository\RequestsRepository($pdo);
});

$container->bind('requestsController', function() use($container){
    $requestsRepository = $container->get('requestsRepository');
    return new App\Admin\Controller\RequestsController($requestsRepository);
});

$container->bind('inboxRepository', function() use($container){
    $pdo = $container->get('pdo');
    return new App\Repository\InboxRepository($pdo);
});