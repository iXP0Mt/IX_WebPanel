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
                    $listInitPlugins[] = $pluginDatabase;
                }
                $isFind = true;
            }

            if(!$isFind) {
                $pluginDatabase['enabled'] = "ERROR_EXIST";
                $listInitPlugins[] = $pluginDatabase;
            }

        }

        return $listInitPlugins;
    }
}