<?php

use routes\Router;

$router = new Router('app');

$router->get('/', Controller_Panel::class, 'index');

$router->get('/exit', Controller_Panel::class, 'exit');
$router->post('/exit', Controller_Panel::class, 'postExit');

$router->get('/login', Controller_Login::class, 'index');
$router->post('/login', Controller_Login::class, 'login');

$router->get('/registration', Controller_Registration::class, 'index');
$router->post('/registration', Controller_Registration::class, 'registration');

$router->get('/plugin', Controller_Plugin::class, 'index');
$router->get('/plugin/init/:pluginTechName', Controller_Plugin::class, 'init');
$router->post('/plugin/init/:pluginTechName', Controller_Plugin::class, 'postInit');
$router->get('/plugin/edit/:pluginId', Controller_Plugin::class, 'edit');
$router->post('/plugin/edit/:pluginId', Controller_Plugin::class, 'postEdit');
$router->get('/plugin/:pluginId/...', Controller_Plugin::class, 'viewPlugin');

$router->get('/admins', Controller_Admins::class, 'index');
$router->get('/admins/add', Controller_Admins::class, 'add');
$router->post('/admins/add', Controller_Admins::class, 'postAdd');
$router->get('/admins/edit/:adminId', Controller_Admins::class, 'edit');
$router->post('/admins/edit/:adminId', Controller_Admins::class, 'postEdit');
$router->get('/admins/delete/:adminId', Controller_Admins::class, 'delete');
$router->post('/admins/delete/:adminId', Controller_Admins::class, 'postDelete');


$router->dispatch();