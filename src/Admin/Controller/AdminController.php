<?php

namespace App\Admin\Controller;

use App\Admin\Support\AuthService;

class AdminController {

    public function __construct(protected AuthService $authService){}

    public function render($view, $params) { 
        extract($params);

        ob_start();
        require __DIR__ . '/../../../views/admin/pages/' . $view . '.php';
        $contents = ob_get_clean();
        
        require __DIR__ . '/../../../views/admin/layouts/main.view.php';
    }

}