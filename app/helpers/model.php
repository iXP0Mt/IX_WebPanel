<?php

namespace app\helpers;

class Model extends Database
{
    /**
     * Проверяет входящие POST-параметры на корректность.
     * Если что-то не так, возвращает сообщение с ошибкой, иначе true.
     *
     * @return bool|string Если что-то не так, возвращает сообщение с ошибкой, иначе true.
     */
    protected function validatePost(): bool|string
    {
        return false;
    }

    /**
     * Проверяет входящие GET-параметры на корректность.
     * Если что-то не так, возвращает сообщение с ошибкой, иначе true.
     *
     * @return bool|string
     */
    protected function validateGet(): bool|string
    {
        return false;
    }

    /**
     * @return void
     */
    protected function forceValidateGet()
    {
    }


    protected function getAdminById(int $adminId): ?array
    {
        return self::selectAdminById($adminId);
    }

    protected function getAdminFlagsById(int $adminId): ?string
    {
        $admin = $this->getAdminById($adminId);
        if (empty($admin)) return null;

        return $admin['flags'];
    }

    protected function getAdminLoginById(int $adminId): ?string
    {
        $admin = $this->getAdminById($adminId);
        if (empty($admin)) return null;

        return $admin['login'];
    }

    function flashErrorMessage(string $message = null): ?string
    {
        if($message === null) {
            if (isset($_SESSION['ERR_MSG'])) {
                $temp = $_SESSION['ERR_MSG'];
                unset($_SESSION['ERR_MSG']);
                return $temp;
            }
        } else {
            $_SESSION['ERR_MSG'] = $message;
        }

        return null;
    }

    function flashSuccessMessage(string $message = null): ?string
    {
        if($message === null) {
            if (isset($_SESSION['SUC_MSG'])) {
                $temp = $_SESSION['SUC_MSG'];
                unset($_SESSION['SUC_MSG']);
                return $temp;
            }
        } else {
            $_SESSION['SUC_MSG'] = $message;
        }

        return null;
    }
}