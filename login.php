<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

session_start();

// include your existing PDO connector (CLAMV creds)
require __DIR__ . '/../EasySearch_php/db.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['username'] ?? '');
  $p = $_POST['password'] ?? '';

  if ($u === '' || $p === '') {
    $err = 'Please enter username and password';
  } else {
    $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM app_users WHERE username = :u LIMIT 1");
    $stmt->execute([':u' => $u]);
    $user = $stmt->fetch();

    if ($user && password_verify($p, $user['password_hash'])) {
      session_regenerate_id(true);
      $_SESSION['uid']  = $user['id'];
      $_SESSION['user'] = $user['username'];
      $_SESSION['role'] = $user['role'];
      header('Location: /~nakbogha/admin/index.html');
      exit;
    } else {
      $err = 'Invalid username or password';
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/~nakbogha/style.css">
  <style>
    body{font-family:Segoe UI,Arial,sans-serif;background:#FAF8F5;margin:0}
    .wrap{max-width:380px;margin:60px auto;background:#fff;padding:24px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,.08)}
    h2{color:#193e65;margin-top:0}
    label{display:block;margin:10px 0 4px;font-weight:600}
    input{width:100%;padding:12px;border:1px solid #d7dbe0;border-radius:10px}
    button{margin-top:14px;background:#193e65;color:#fff;border:0;padding:12px 18px;border-radius:8px;cursor:pointer}
    .err{color:#b00020;margin:8px 0}
  </style>
</head>
<body>
  <div class="wrap">
    <h2>Admin Login</h2>
    <?php if ($err): ?><div class="err"><?=htmlspecialchars($err)?></div><?php endif; ?>
    <form method="post" autocomplete="off">
      <label>Username</label>
      <input name="username" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <button>Sign in</button>
    </form>
    <p style="margin-top:12px"><a href="/~nakbogha/index.html">← Back to Home</a></p>
  </div>
</body>
</html>
