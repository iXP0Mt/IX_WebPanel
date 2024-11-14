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
        return $this->getListPluginsFromDir($techName);
    }

    /**
     * Проверяет POST параметры в соответствии с требуемыми параметрами.
     *
     *
     * В частности здесь проверяются соответствие всех настроек плагина.
     *
     * @param array $requiredSettings
     * @return bool|string Возвращает true, если всё успешно. Иначе сообщение с ошибкой.
     */
    function validatePost2(array $requiredSettings): bool|string
    {
        if(!isset($_POST['name'])) {
            return 'Поле названия должно быть заполнено';
        }

        $name = trim($_POST['name']);

        if(empty($name)) {
            return 'Поле названия должно быть заполнено';
        }

        if (strlen($name) > 128) {
            return "Название не более 128 символов.";
        }

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

    /**
     * Комплексная проверка инициализированного плагина.
     * 1. Проверка, что плагин есть в базе данных.
     * 2. Проверка, что плагина есть в локальной директории.
     * 3. Версии локального плагина и плагина из БД совпадают.
     *
     * Если задан $plugin, то запишет в него плагин с объединенной информацией из БД и локальной директории.
     *
     * @param int $id
     * @param array|null $plugin Возвращает сюда плагин при успешной проверке.
     * @return string|bool Возвращает true если плагин успешно проверен. String сообщение с ошибкой.
     */
    function complexCheckInitPlugin(int $id, array &$plugin = null): string|bool
    {
        /*
        При получении плагина с базы данных проверяем его что он валидный, а именно:
        - Такой плагин есть в БД
        - Такой плагин есть в локальной папке
        - Версии плагинов совпадают
        */
        $pluginDatabase = self::selectPluginById($id);
        if($pluginDatabase === null) return "Ошибка получения плагина из базы данных.";
        if(empty($pluginDatabase)) return "Плагин не найден в базе данных.";

        $pluginDir = $this->getPluginFromDir($pluginDatabase['techName']);
        if($pluginDir === null) return "Ошибка получения плагина из локальной директории.";
        if(empty($pluginDir)) return "Плагин не найден в локальной директории.";

        if($pluginDatabase['version'] != $pluginDir['version']) return "Версии плагинов не совпадают";

        if($plugin !== null) $plugin = $this->mergeInfosPlugin($pluginDatabase, $pluginDir);

        return true;
    }

    private function mergeInfosPlugin(array $pluginDatabase, array $pluginDir): array
    {
        $result = [
            'tech_name' => $pluginDir['tech_name'],
            'name' => $pluginDatabase['name'],
            'version' => $pluginDir['version'],
            'dir' => $pluginDir['dir'],
            'enabled' => $pluginDatabase['enabled']
        ];

        foreach ($pluginDir['settings'] as $key => $description) {
            $result['settings'][$key] = [
                'description' => $description,
                'value' => $pluginDatabase['settings'][$key]
            ];
        }

        return $result;
    }

    function isPluginExistInDatabase(mixed $determinant): ?bool
    {
        if(is_int($determinant))
            $result = self::selectPluginById($determinant);
        else if (is_string($determinant))
            $result = self::selectPluginByTechName($determinant);
        else return null;

        return !!($result) ?? null;
    }

    function isPluginExistInDir(string $techName): ?bool
    {
        $plugin = $this->getListPluginsFromDir($techName);
        return !!($plugin) ?? null;
    }

    function editPlugin(int $pluginId, array $dataPost): ?bool
    {
        $name = $dataPost['name'];
        unset($dataPost['name']);
        $settings = $dataPost;

        $settingsJson = json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if($settingsJson === false) return null;

        return self::updatePluginById(
            pluginId: $pluginId,
            name: $name,
            settings: $settingsJson
        );
    }
}