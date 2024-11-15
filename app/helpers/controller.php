<?php

namespace app\helpers;

use app\models\Model_Template;

class Controller
{
    public View $view;
    public Model $model;

    /**
     * Использовать шаблон, включающий в себя панели навигации и управления.
     *
     * @param array $data
     * @return void
     */
    function useTemplate(array &$data): void
    {
        $modelPath = 'app/models/model_template.php';
        if(!file_exists($modelPath)) {
            echo "ФАЙЛ ШАБЛОНА НЕ СУЩЕСТВУЕТ";
            die;
        }

        include $modelPath;

        $templateModel = new Model_Template();

        $data['template']['isVisibleItems'] = $templateModel->isVisibleMainAdminItems();
        $data['template']['plugins'] = $templateModel->getInitPlugins();
    }
}