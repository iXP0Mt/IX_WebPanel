<?php

use app\helpers\Controller;
use app\helpers\View;
use app\models\Model_Login;
use JetBrains\PhpStorm\NoReturn;

class Controller_Login extends Controller
{
    public function __construct()
    {
        $this->view = new View();
        $this->model = new Model_Login();
    }

    function index()
    {
        $data['content']['error_msg'] = $this->model->flashErrorMessage();

        $this->view->render('app/views/view_login.php', $data);
    }

    #[NoReturn] function login()
    {
        $resultValidate = $this->model->validatePost();
        if($resultValidate !== true) {
            $this->model->flashErrorMessage($resultValidate);
            header('Location: /login');
            exit;
        }

        $resultAuth = $this->model->authorization();
        if($resultAuth !== true) {
            $this->model->flashErrorMessage($resultAuth);
            header('Location: /login');
            exit;
        }

        header('Location: /');
        exit;
    }
}