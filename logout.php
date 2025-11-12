<?php
session_start();
$_SESSION = [];
session_destroy();
header('Location: /~nakbogha/admin/login.php');
exit;
