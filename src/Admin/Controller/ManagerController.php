<?php

namespace App\Admin\Controller;

class ManagerController {

    public function render_admin($view, $params) {
        extract($params);

        ob_start();
        require __DIR__ . '/../../../views/admin/pages/' . $view . '.php';
        $contents = ob_get_clean();
        
        require __DIR__ . '/../../../views/admin/layouts/admin.view.php';
    }
    
    public function render($view, $params) { 
        extract($params);

        ob_start();
        require __DIR__ . '/../../../views/admin/pages/' . $view . '.php';
        $contents = ob_get_clean();
        
        require __DIR__ . '/../../../views/admin/layouts/main.view.php';
    }

    public function ShowPanel(){
        $this->render_admin('control', []);
    }

    public function ShowHelp(bool $status, bool $isadmin){
        $this->render('help', [
            'status' => $status,
            'isadmin' => $isadmin
        ]);
    }

    public function showEmailSent(bool $status, bool $isadmin){
        $this->render('email.sent', [
            'status' => $status,
            'isadmin' => $isadmin
        ]);
    }

    public function showEmailSentAcc(bool $status, bool $isadmin){
        $this->render('email.sent.acc', [
            'status' => $status,
            'isadmin' => $isadmin
        ]);
    }

    public function showProfile($entries, $user, bool $isadmin, bool $isProfileOwner, string $status, int $page_num){
        
        $page = (int)($_GET['page'] ?? 1);
        
        $this->render_admin('profile', [
            'entries' => $entries,
            'user' => $user,
            'isadmin' => $isadmin,
            'isprofowner' => $isProfileOwner,
            'status' => $status,
            'page'=> $page,
            'num_pages' => $page_num
        ]);
    }

    public function json($data, $code = 200){
        
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
}