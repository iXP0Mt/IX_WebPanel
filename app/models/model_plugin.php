<?php

use app\helpers\Model;

class Model_Plugin extends Model
{
    function getPlugins(): ?array
    {
        $pluginsFromDir = $this->getListPluginsFromDir();
        if($pluginsFromDir === null) return null;

        $pluginsFromDataBase = self::selectPlugins();
        if($pluginsFromDataBase === null) return null;

        $plugins['non_init'] = $this->getNonInitPlugins($pluginsFromDir, $pluginsFromDataBase);
        $plugins['init'] = $this->getInitPlugins($pluginsFromDir, $pluginsFromDataBase);

        return $plugins;
    }

    private function getNonInitPlugins(array $pluginsFromDir, array $pluginsFromDataBase): array
    {
        $listNonInitPlugins = [];
        foreach ($pluginsFromDir as $pluginDir) {
            $isInit = false;
            foreach ($pluginsFromDataBase as $pluginDatabase) {
                if($pluginDir['tech_name'] == $pluginDatabase['techName']) {
                    $isInit = true;
                    break;
                }
            }
            if(!$isInit) {
                $listNonInitPlugins[] = $pluginDir;
            }
        }

        return $listNonInitPlugins;
    }

    private function getInitPlugins(array $pluginsFromDir, array $pluginsFromDataBase): array
    {
        $listInitPlugins = [];
        foreach ($pluginsFromDataBase as $pluginDatabase) {
            $isFind = false;
            foreach ($pluginsFromDir as $pluginDir) {
                if($pluginDatabase['techName'] != $pluginDir['tech_name']) {
                    continue;
                }

                if($pluginDatabase['version'] != $pluginDir['version']) {
                    $pluginDatabase['enabled'] = "ERROR_VERSION";
                }

                $isFind = true;
                break;
            }

            if(!$isFind) {
                $pluginDatabase['enabled'] = "ERROR_EXIST";
            }

            $listInitPlugins[] = $pluginDatabase;
        }

        return $listInitPlugins;
    }

    function getPluginFromDir(string $techName): ?array
    {
        $listPlugins = $this->getListPluginsFromDir();
        if($listPlugins === null) return null;

        foreach ($listPlugins as $plugin) {
            if($plugin['tech_name'] == $techName) {
                return $plugin;
            }
        }

        return [];
    }

    function isPluginExistInDatabase(string $techName): ?bool
    {
        $plugin = self::selectPluginByTechName($techName);
        if($plugin === null) return null;

        if(empty($plugin)) return false;

        return true;
    }

    function validatePost2(array $requiredSettings): bool|string
    {
        foreach ($requiredSettings as $setting => $desc) {
            if(!isset($_POST[$setting])) {
                return "Ошибка чтения настроек доступов (флагов).";
            }

            $flags = trim($_POST[$setting]);

            if(strlen($flags) > 0) {
                $resultValid = $this->checkValidStringFlags($flags);
                if($resultValid !== true) {
                    return $resultValid;
                }
            }

            $_POST[$setting] = $flags;
        }

        return true;
    }

    function prepareSettings(array &$settings)
    {
        foreach ($settings as $setting => $desc) {
            $settings[$setting] = $_POST[$setting];
        }
    }

    /**
     * Подготавливает настройки плагина к внесению в базу данных.
     * Совершает запрос в базу данных.
     * Возвращает ID внесенного плагина.
     *
     * @param string $techName
     * @param string $name
     * @param string $version
     * @param array $settings
     * @return int|null
     */
    function addPlugin(string $techName, string $name, string $version, array $settings): ?int
    {
        $settingsJson = json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if($settingsJson === false) return null;

        return self::insertPlugin($techName, $name, $version, $settingsJson);
    }

    function getPluginById(int $id): ?array
    {
        return self::selectPluginById($id);
    }
}