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

function isAdmin()
{
    if (!isset($_SESSION['user'])) {
        return false;
    }
    if (isset($_SESSION['user']['vai_tro']) && $_SESSION['user']['vai_tro'] === ROLE_ADMIN) {
        return true;
    }
    return false;
}

function requireAdmin()
{
    if (!isAdmin()) {
        echo "<script>
                alert('Bạn không có quyền thực hiện thao tác này!');
                window.location.href = '" . BASE_URL . "';
              </script>";
        exit();
    }
}
