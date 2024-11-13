<?php

namespace app\models;

use app\helpers\Model;

class Model_Admins extends Model
{
    function forceValidateGet(): void
    {
        if(
            !isset($_GET['page']) ||
            !is_numeric($_GET['page']) ||
            $_GET['page'] > 100
        ) {
            $this->default_page();
        }
    }

    /**
     * Устанавливает GET-параметру page стандартное значение.
     *
     * @return void
     */
    private function default_page(): void
    {
        $_GET['page'] = 1;
    }

    function getListAdmins($page): array|string
    {
        $listAdmins = self::selectAdmins(5, $page);
        if($listAdmins === null) {
            return "Ошибка запроса на получение админов.";
        }

        return $listAdmins;
    }

    function isAccess(): bool
    {
        $flags = $this->getAdminFlagsById($_SESSION['user_id']);
        if($flags === null) {
            return false;
        }

        if(str_contains($flags, 'a')) {
            return true;
        }

        return false;
    }

    function validateAddPost(): bool|string
    {
        if(
            !isset($_POST['username']) ||
            !isset($_POST['password']) ||
            !isset($_POST['password_confirm']) ||
            !isset($_POST['flags'])
        ) {
            return 'Все поля должны быть заполнены';
        }

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['password_confirm']);
        $flags = trim($_POST['flags']);

        if (empty($username) || empty($password) || empty($confirmPassword) || empty($flags)) {
            return "Все поля должны содержать данные.";
        }

        if (strlen($password) < 4 || strlen($password) > 32) {
            return "Пароль должен содержать от 4 до 32 символов.";
        }

        if ($password !== $confirmPassword) {
            return "Пароли не совпадают.";
        }

        // Проверяем, что строка состоит только из английских букв
        if (!preg_match('/^[a-z]+$/', $flags)) {
            return "Флаги могут состоять только из букв английского алфавита нижнего регистра.";
        }

        $letters = str_split($flags);
        if(count($letters) !== count(array_unique($letters))) {
            return "Флаги не должны повторяться.";
        }

        $_POST['username'] = $username;
        $_POST['password'] = $password;
        $_POST['password_confirm'] = $confirmPassword;
        $_POST['flags'] = $flags;

        return true;
    }

    function validatePostEdit(): bool|string
    {
        if(
            !isset($_POST['username']) ||
            !isset($_POST['password']) ||
            !isset($_POST['password_confirm']) ||
            !isset($_POST['flags'])
        ) {
            return 'Поля логина и флагов должны быть заполнены';
        }

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $passwordConfirm = trim($_POST['password_confirm']);
        $flags = trim($_POST['flags']);

        if (empty($username)) {
            return "Поле логина должны содержать данные.";
        }

        if (!preg_match('/^[a-z]+$/', $flags)) {
            return "Флаги могут состоять только из букв английского алфавита нижнего регистра.";
        }

        $letters = str_split($flags);
        if(count($letters) !== count(array_unique($letters))) {
            return "Флаги не должны повторяться.";
        }

        if(
            !empty($_POST['password']) ||
            !empty($_POST['password_confirm'])
        ) {
            if(
                empty($_POST['password']) ||
                empty($_POST['password_confirm'])
            ) {
                return "При задании нового пароля, поля для пароля и подтверждения пароля должны быть заполнены.";
            }

            $password = trim($_POST['password']);
            $confirmPassword = trim($_POST['password_confirm']);

            if (strlen($password) < 4 || strlen($password) > 32) {
                return "Пароль должен содержать от 4 до 32 символов.";
            }

            if ($password !== $confirmPassword) {
                return "Пароли не совпадают.";
            }
        }

        $_POST['username'] = $username;
        $_POST['flags'] = $flags;
        $_POST['password'] = $password;
        $_POST['password_confirm'] = $passwordConfirm;

        return true;
    }

    function createAdmin(): int|string
    {
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $result = self::insertAdmin($_POST['username'], $hashedPassword, $_POST['flags']);

        if($result === null) {
            return "Ошибка запроса на создание нового админа.";
        }

        return $result;
    }

    function getAdminLogin(int $adminId): ?string
    {
        return $this->getAdminLoginById($adminId);
    }

    function handleAdd(): void
    {
        $resultValidate = $this->validateAddPost();
        if($resultValidate !== true) {
            $this->flashErrorMessage($resultValidate);
            return;
        }

        $resultCreateAdmin = $this->createAdmin();
        if(!is_int($resultCreateAdmin)) {
            $this->flashErrorMessage($resultCreateAdmin);
            return;
        }

        $adminLogin = $this->getAdminLogin($resultCreateAdmin);
        if($adminLogin === null) {
            $this->flashErrorMessage("Ошибка запроса логина админа.");
            return;
        }

        $this->flashSuccessMessage("Админ $adminLogin успешно добавлен!");
    }

    function getAdmin(int $adminId): ?array
    {
        return $this->getAdminById($adminId);
    }

    function editAdmin(int $adminId): string|bool
    {
        $result = self::updateAdminById(
            $adminId,
            $_POST['username'],
            empty($_POST['password']) ? null : password_hash($_POST['password'], PASSWORD_DEFAULT),
            $_POST['flags']
        );
        if($result !== true) {
            return "Ошибка обновления данных.";
        }

        return true;
    }

    function deleteAdmin(int $adminId): string|bool
    {
        $result = self::deleteAdminById($adminId);
        if($result !== true) {
            return "Ошибка удаления админа.";
        }

        return true;
    }
}