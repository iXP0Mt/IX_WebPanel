<?php

namespace app\helpers;

class View
{
    function render(
        string $viewContentName,
        array $data = null
    ) {
        if(isset($data['template'])) {
            include 'app/views/view_template.php';
        } else {
            include $viewContentName;
        }
    }
}