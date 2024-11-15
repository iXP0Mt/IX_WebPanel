<?php

namespace app\models;

use app\helpers\Model;

class Model_Template extends Model
{
    function isVisibleMainAdminItems(): bool
    {
        //$flags = self::selectAdminFlagsById($_SESSION['user_id']);
        $flags = $this->getAdminFlagsById($_SESSION['user_id']);
        if($flags === null) {
            return false;
        }

        if(str_contains($flags, 'a')) {
            return true;
        }

        return false;
    }

    function getInitPlugins(): array
    {
        return self::selectPluginsByEnabled() ?? [];
    }
}