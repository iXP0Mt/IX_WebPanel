<?php

use app\helpers\Controller;
use app\helpers\View;
use app\models\Model_Admins;
use JetBrains\PhpStorm\NoReturn;

class Controller_Admins extends Controller
{
    public function __construct()
    {
        $this->model = new Model_Admins();

        if(!$this->model->isAccess()) {
            header('Location: /');
            exit;
        }

        $this->view = new View();
    }

    function index(): void
    {
        $this->model->forceValidateGet();

        $data['content']['error_msg'] = $this->model->flashErrorMessage();
        $data['content']['success_msg'] = $this->model->flashSuccessMessage();

        $listAdmins = $this->model->getListAdmins($_GET['page']);
        if(is_string($listAdmins)) {
            $data['content']['error_msg'] = $listAdmins;
        } else {
            $data['content']['listAdmins'] = $listAdmins;
        }

        $this->useTemplate($data);
        $this->view->render('app/views/view_admins.php', $data);
    }

    function add()
    {
        $data['content']['error_msg'] = $this->model->flashErrorMessage();
        $data['content']['success_msg'] = $this->model->flashSuccessMessage();

        $this->useTemplate($data);
        $this->view->render('app/views/view_admins_add.php', $data);
    }

    #[NoReturn] function postAdd()
    {
        $this->model->handleAdd();
        header('Location: /admins/add');
        exit;
    }

    function edit(int $adminId)
    {
        $data['content']['error_msg'] = $this->model->flashErrorMessage();

        $admin = $this->model->getAdmin($adminId);
        if(empty($admin)) {
            $data['content']['error_msg'] = "Ошибка получения данных.";
        } else {
            $data['content']['login'] = $admin['login'];
            $data['content']['flags'] = $admin['flags'];
        }

        $this->useTemplate($data);
        $this->view->render('app/views/view_admins_edit.php', $data);
    }

    #[NoReturn] function postEdit(int $adminId)
    {
       $resultValidate = $this->model->validatePostEdit();
       if($resultValidate !== true) {
           $this->model->flashErrorMessage($resultValidate);
           header("Location: /admins/edit/$adminId");
           exit;
       }

       $result = $this->model->editAdmin($adminId);
       if($result !== true) {
           $this->model->flashErrorMessage($result);
           header("Location: /admins/edit/$adminId");
           exit;
       }

       $this->model->flashSuccessMessage("Админ [id=$adminId] успешно изменён.");
       header("Location: /admins");
       exit;
    }

    function delete(int $adminId)
    {
        $adminName = $this->model->getAdminLogin($adminId);
        if(empty($adminName)) {
            $this->model->flashErrorMessage("Ошибка получения админа для удаления.");
            header("Location: /admins");
            exit;
        }

        $data['content']['login'] = $adminName;

        $this->useTemplate($data);
        $this->view->render('app/views/view_admins_delete.php', $data);
    }

    #[NoReturn] function postDelete(int $adminId)
    {
        $result = $this->model->deleteAdmin($adminId);
        if($result === true) {
            $this->model->flashSuccessMessage("Админ [id=$adminId] успешно удалён.");
        } else {
            $this->model->flashErrorMessage($result);
        }

        header("Location: /admins");
        exit;
    }
}