<?php
require_once __DIR__ . '/../../bootstrap.php';

$_SESSION = array();
session_unset();
session_destroy();

header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
exit();
