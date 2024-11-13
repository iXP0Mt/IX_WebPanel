<?php

namespace app\models;

use app\helpers\Model;

class Model_Registration extends Model
{
    function validatePost(): bool|string
    {
        if(
            !isset($_POST['username']) ||
            !isset($_POST['password']) ||
            !isset($_POST['confirm_password'])
        ) {
            return 'Все поля должны быть заполнены';
        }

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirm_password']);

        if (empty($username) || empty($password) || empty($confirmPassword)) {
            return "Все поля должны содержать данные.";
        }

        if (strlen($password) < 4 || strlen($password) > 32) {
            return "Пароль должен содержать от 4 до 32 символов.";
        }

        if ($password !== $confirmPassword) {
            return "Пароли не совпадают.";
        }

        $_POST['username'] = $username;
        $_POST['password'] = $password;
        $_POST['confirm_password'] = $confirmPassword;

        return true;
    }


    function registration(): bool|string
    {
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $flags = "a";
        $result = self::insertAdmin($_POST['username'], $hashedPassword, $flags);

        if($result === null) {
            return "Ошибка запроса на регистрацию.";
        }

        $_SESSION['user_id'] = $result;

        return true;
    }
}