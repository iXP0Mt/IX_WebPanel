<?php

use app\helpers\Controller;
use app\helpers\View;

class Controller_Plugin extends Controller
{
    public function __construct()
    {
        $this->view = new View();
        $this->model = new Model_Plugin();
    }

    function index()
    {
        //include 'plugins/iX_Stats/index.php';

        $listPlugins = $this->model->getPlugins();
        if($listPlugins === null) {
            $data['content']['error_msg'] = "Ошибка получения плагинов из директории plugins.";
        } else {
            $data['content']['non_init_plugins'] = $listPlugins['non_init'];
            $data['content']['init_plugins'] = $listPlugins['init'];
        }

        $this->useTemplate($data);
        $this->view->render("app/views/view_plugin.php", $data);
    }
}