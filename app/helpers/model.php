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

    /**
     * Получает все плагины из локальной директории.
     * Если указан $techName, то пробует получить плагин с таким техническим названием.
     *
     * @param string|null $techName
     * @return array|null
     */
    protected function getListPluginsFromDir(string $techName = null): ?array
    {
        $pluginsPath = $_SERVER['DOCUMENT_ROOT'].'/plugins';

        if(!is_dir($pluginsPath)) return null;

        $listFiles = scandir($pluginsPath);
        if($listFiles === false) return null;

        $listFiles = array_slice($listFiles, 2);

        $plugins = [];

        foreach ($listFiles as $file) {
            $dirPath = $pluginsPath.'/'.$file;
            if(!is_dir($dirPath)) continue;

            $pluginConfigPath = $pluginsPath.'/'.$file.'/config.json';
            if(!file_exists($pluginConfigPath)) continue;

            $configJson = json_decode(file_get_contents($pluginConfigPath), true);
            if(
                !isset($configJson['tech_name']) ||
                !isset($configJson['name']) ||
                !isset($configJson['version']) ||
                !isset($configJson['settings'])
            ) continue;

            $configJson['dir'] = $file;

            if($techName !== null) {
                if($techName == $configJson['tech_name']) return $configJson;
                else continue;
            }

            $plugins[] = $configJson;
        }

        return $plugins;
    }

    /**
     * Проверить строку с флагами доступа на валидность.
     *
     * @param string $flags
     * @return string|bool
     */
    protected function checkValidStringFlags(string $flags): string|bool
    {
        // Проверяем, что строка состоит только из английских букв
        if (!preg_match('/^[a-z]+$/', $flags)) {
            return "Флаги могут состоять только из букв английского алфавита нижнего регистра.";
        }

        $letters = str_split($flags);
        if(count($letters) !== count(array_unique($letters))) {
            return "Флаги не должны повторяться.";
        }

        return true;
    }
}