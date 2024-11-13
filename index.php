<?php

ini_set('display_errors', 1);

if(session_status() === PHP_SESSION_NONE)
    session_start();

require_once 'app/helpers/database.php';
require_once 'app/helpers/middleware_auth.php';

require_once 'app/helpers/model.php';
require_once 'app/helpers/controller.php';
require_once 'app/helpers/view.php';

require_once 'routes/router.php';
require_once 'routes/web.php';