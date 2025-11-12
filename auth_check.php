<?php
session_start();
if (empty($_SESSION['uid']) || ($_SESSION['role'] ?? '') !== 'admin') {
  http_response_code(403);
  echo '<p>Unauthorized. <a href="/~nakbogha/admin/login.php">Login</a></p>';
  exit;
}
