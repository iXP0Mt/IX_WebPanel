<?php

use app\helpers\Controller;
use app\helpers\View;
use app\models\Model_Registration;
use JetBrains\PhpStorm\NoReturn;

class Controller_Registration extends Controller
{
    public function __construct()
    {
        $this->view = new View();
        $this->model = new Model_Registration();
    }

    function index(): void
    {
        $data['content']['error_msg'] = $this->model->flashErrorMessage();
        $data['content']['success_msg'] = $this->model->flashSuccessMessage();

        $this->view->render('app/views/view_registration.php', $data);
    }

    #[NoReturn] function registration(): void
    {
        $resultValidate = $this->model->validatePost();
        if($resultValidate !== true) {
            $this->model->flashErrorMessage($resultValidate);
            header('Location: /registration');
            exit;
        }

        $resultRegistration = $this->model->registration();
        if($resultRegistration !== true) {
            $this->model->flashErrorMessage($resultRegistration);
            header('Location: /registration');
            exit;
        }

        header('Location: /');
        exit;
    }
}