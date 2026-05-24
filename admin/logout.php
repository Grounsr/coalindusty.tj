<?php
require_once __DIR__ . '/_bootstrap.php';
log_action('logout');
$_SESSION = [];
session_destroy();
header('Location: /admin/login.php');
