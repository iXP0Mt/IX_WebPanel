<?php

namespace app\helpers;

class Middleware_Auth
{
    private static function isAuth(): bool
    {
        return !!($_SESSION['user_id'] ?? false);
    }

    private static function isAdminExist(): bool
    {
        return !!(Database::selectValueAdmins() ?? die('Error: #h539t3'));
    }

    static function checkAccess(string $uri): void
    {
        if(self::isAuth()) {
            if($uri == '/login' || $uri == '/registration') {
                header('Location: /');
                exit;
            } else {
                return;
            }
        } else {
            if(self::isAdminExist()) {
                if($uri == '/login') {
                    return;
                } else {
                    header('Location: /login');
                    exit;
                }
            } else {
                if($uri == '/registration') {
                    return;
                } else {
                    header('Location: /registration');
                    exit;
                }
            }
        }
    }
}
