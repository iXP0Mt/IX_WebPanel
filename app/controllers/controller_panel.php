<?php

use app\helpers\Controller;
use app\helpers\View;
use JetBrains\PhpStorm\NoReturn;

class Controller_Panel extends Controller
{
    public function __construct()
    {
        $this->view = new View();
    }

    function index()
    {
        $data = [];
        $this->useTemplate($data);
        $this->view->render('app/views/view_panel.php', $data);
    }

    function exit()
    {
        $data = [];
        $this->useTemplate($data);
        $this->view->render('app/views/view_exit.php', $data);
    }

    #[NoReturn] function postExit()
    {
        session_destroy();
        header("Location: /");
        exit;
    }
}