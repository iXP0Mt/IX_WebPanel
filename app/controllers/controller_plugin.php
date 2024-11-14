<?php

use app\helpers\Controller;
use app\helpers\View;
use JetBrains\PhpStorm\NoReturn;

class Controller_Plugin extends Controller
{
    public function __construct()
    {
        $this->view = new View();
        $this->model = new Model_Plugin();
    }

    function index()
    {
        $data['content']['error_msg'] = $this->model->flashErrorMessage();
        $data['content']['success_msg'] = $this->model->flashSuccessMessage();

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

    function init(string $pluginTechName)
    {
        $plugin = $this->model->getPluginFromDir($pluginTechName);
        if($plugin === null) {
            $this->model->flashErrorMessage("Ошибка проверки плагина $pluginTechName в локальной папке.");
            header("Location: /plugin");
            exit;
        }

        if(empty($plugin)) {
            $this->model->flashErrorMessage("Плагин $pluginTechName не найден в локальной папке.");
            header("Location: /plugin");
            exit;
        }

        $result = $this->model->isPluginExistInDatabase($pluginTechName);
        if($result === null) {
            $this->model->flashErrorMessage("Ошибка проверки плагина $pluginTechName в базе данных.");
            header("Location: /plugin");
            exit;
        }

        if($result === true) {
            $this->model->flashErrorMessage("Плагин $pluginTechName уже инициализирован.");
            header("Location: /plugin");
            exit;
        }

        $data['content']['error_msg'] = $this->model->flashErrorMessage();
        $data['content']['success_msg'] = $this->model->flashSuccessMessage();

        $data['content']['plugin'] = $plugin;

        $this->useTemplate($data);
        $this->view->render("app/views/view_plugin_init.php", $data);

        //include "plugins/$dirName/index.php";
    }

    #[NoReturn] function postInit(string $pluginTechName)
    {
        $plugin = $this->model->getPluginFromDir($pluginTechName);
        if($plugin === null) {
            $this->model->flashErrorMessage("Ошибка получения плагина из локальной папки.");
            header("Location: /plugin/init/$pluginTechName");
            exit;
        }

        if(empty($plugin)) {
            $this->model->flashErrorMessage("Плагин $pluginTechName не найден в локальной папке.");
            header("Location: /plugin/init/$pluginTechName");
            exit;
        }

        $resultValidate = $this->model->validatePost2($plugin['settings']);
        if($resultValidate !== true) {
            $this->model->flashErrorMessage($resultValidate);
            header("Location: /plugin/init/$pluginTechName");
            exit;
        }

        $this->model->prepareSettings($plugin['settings']);

        $pluginId = $this->model->addPlugin(
            $plugin['tech_name'],
            $plugin['name'],
            $plugin['version'],
            $plugin['settings']
        );
        if($pluginId === null) {
            $this->model->flashErrorMessage("Ошибка добавления плагина в базу данных.");
            header("Location: /plugin/init/$pluginTechName");
            exit;
        }

        $plugin = $this->model->getPluginById($pluginId);
        if($plugin === null) {
            $this->model->flashErrorMessage("Операция добавления плагина в базу данных была выполнена, но получить новый плагин не получилось.");
            header("Location: /plugin/init/$pluginTechName");
            exit;
        }

        $this->model->flashSuccessMessage('Плагин '.$plugin['name'].' успешно инициализирован.');
        header("Location: /plugin");
        exit;
    }

    function edit(int $pluginId)
    {
        $plugin = [];
        $result = $this->model->complexCheckInitPlugin($pluginId, $plugin);
        if($result !== true) {
            $this->model->flashErrorMessage($result);
            header("Location: /plugin");
            exit;
        }

        $data['content']['error_msg'] = $this->model->flashErrorMessage();
        $data['content']['success_msg'] = $this->model->flashSuccessMessage();

        $data['content']['plugin'] = $plugin;

        $this->useTemplate($data);
        $this->view->render("app/views/view_plugin_edit.php", $data);
    }

    #[NoReturn] function postEdit(int $pluginId)
    {
        $result = $this->model->complexCheckInitPlugin($pluginId);
        if($result !== true) {
            $this->model->flashErrorMessage($result);
            header("Location: /plugin/edit/$pluginId");
            exit;
        }

        $plugin = $this->model->getPluginById($pluginId);
        if($plugin === null) {
            $this->model->flashErrorMessage("Ошибка получения плагина из базы данных.");
            header("Location: /plugin/edit/$pluginId");
            exit;
        }

        $result = $this->model->processToggleButton($pluginId, $plugin['enabled']);
        if($result === true) {
            header("Location: /plugin/edit/$pluginId");
            exit;
        }

        $resultValidate = $this->model->validatePost2($plugin['settings']);
        if($resultValidate !== true) {
            $this->model->flashErrorMessage($resultValidate);
            header("Location: /plugin/edit/$pluginId");
            exit;
        }

        $result = $this->model->editPlugin($pluginId, $_POST);
        if($result === null) {
            $this->model->flashErrorMessage("Ошибка изменения плагина.");
            header("Location: /plugin/edit/$pluginId");
            exit;
        }
        if($result === false) {
            $this->model->flashErrorMessage("Плагин не был изменён.");
            header("Location: /plugin/edit/$pluginId");
            exit;
        }

        $this->model->flashSuccessMessage("Плагин был успешно изменён.");
        header("Location: /plugin");
        exit;
    }
}