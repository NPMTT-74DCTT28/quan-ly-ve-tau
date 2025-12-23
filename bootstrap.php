<?php
session_set_cookie_params(0);
session_start();

$timeout_duration = 1800;

if (isset($_SESSION['LAST_ACTIVITY'])) {
    if ((time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        session_unset();
        session_destroy();
    }
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
$db = new Database();
