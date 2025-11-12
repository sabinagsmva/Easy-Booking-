<?php
ini_set('display_errors',1); error_reporting(E_ALL);
require __DIR__.'/db.php';

$q = trim($_GET['q'] ?? '');
if ($q === '') exit('Missing query. <a href="search_customers_form.php">Back</a>');

$sql = "
SELECT u.user_id, u.email, COUNT(b.booking_id) AS total_bookings
FROM users u
JOIN guests g ON g.user_id = u.user_id
LEFT JOIN bookings b ON b.guest_id = g.user_id
WHERE u.email LIKE :q
GROUP BY u.user_id, u.email
ORDER BY u.email;
";
$st = $pdo->prepare($sql);
$st->execute([':q'=>"%$q%"]);
$rows = $st->fetchAll();

function h($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Customer Results</title>
  <style>
    body{font-family:Segoe UI,Arial,sans-serif;background:#FAF8F5;margin:0;color:#333}
    .wrap{max-width:960px;margin:34px auto;padding:0 16px}
    .card{background:#fff;border:1px solid #e7e9ee;border-radius:12px;padding:14px 16px;margin:10px 0;box-shadow:0 6px 16px rgba(0,0,0,.06)}
    .muted{color:#6b7280}
    .btn{display:inline-block;margin-top:8px;text-decoration:none;color:#193e65;border:1px solid #dbe2f2;padding:8px 12px;border-radius:8px;font-weight:700;background:#f5f7fb}
    .btn:hover{background:#eef3fb}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Customer Results for “<?=h($q)?>”</h1>
    <?php if (!$rows): ?>
      <p>No results.</p>
      <p><a class="btn" href="search_customers_form.php">← New Search</a></p>
    <?php else: foreach ($rows as $r): ?>
      <div class="card">
        <strong><?=h($r['email'])?></strong><br>
        Total Bookings: <?=h($r['total_bookings'])?><br>
        <a class="btn" href="customer_detail.php?id=<?=h($r['user_id'])?>">View details</a>
      </div>
    <?php endforeach; endif; ?>
    <p><a class="btn" href="search_customers_form.php">← Back</a></p>
    <p><a class="btn" href="/~nakbogha/admin/index.html">← Back to Admin Hub</a></p>
  </div>
</body>
</html>
