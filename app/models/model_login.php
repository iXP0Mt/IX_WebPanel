<?php

namespace app\models;

use app\helpers\Model;

class Model_Login extends Model
{
    function validatePost(): bool|string
    {
        if(
            !isset($_POST['username']) ||
            !isset($_POST['password'])
        ) {
            return 'Все поля должны быть заполнены';
        }

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (empty($username) || empty($password)) {
            return "Все поля должны содержать данные.";
        }

        if (strlen($password) < 4 || strlen($password) > 32) {
            return "Пароль должен содержать от 4 до 32 символов.";
        }

        $_POST['username'] = $username;
        $_POST['password'] = $password;

        return true;
    }

    function authorization(): bool|string
    {
        $admin = self::selectAdminByLogin($_POST['username']);
        if(empty($admin)) {
            return "Неверный логин или пароль.";
        }

        if(!password_verify($_POST['password'], $admin['password'])) {
            return "Неверный логин или пароль.";
        }

        $_SESSION['user_id'] = $admin['id'];

        return true;
    }
}