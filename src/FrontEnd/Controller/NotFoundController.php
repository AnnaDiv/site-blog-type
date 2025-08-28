<?php

namespace App\FrontEnd\Controller;

class NotFoundController extends EntriesController {

    public function error404 (){
        http_response_code(404);
        $this->render_r('notFound', []);
    } 

    public function iseeyou(){
        $this->render_r('iseeyou', []);

    }
}