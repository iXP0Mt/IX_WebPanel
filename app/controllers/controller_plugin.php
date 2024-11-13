<?php

use app\helpers\Controller;
use app\helpers\View;

class Controller_Plugin extends Controller
{
    public function __construct()
    {
        //$this->view = new View();
    }

    function index(int $idPlugin)
    {
        include 'plugins/iX_Stats/index.php';

    }
}