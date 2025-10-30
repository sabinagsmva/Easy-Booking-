<?php
ini_set('display_errors',1); error_reporting(E_ALL);
require __DIR__.'/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) exit('Missing id. <a href="search_customers_form.php">Back</a>');

$sql = "
SELECT u.email,
       COUNT(b.booking_id) AS total_bookings,
       AVG(rv.rating)      AS avg_rating
FROM users u
JOIN guests g ON g.user_id = u.user_id
LEFT JOIN bookings b ON b.guest_id = g.user_id
LEFT JOIN reviews  rv ON rv.booking_id = b.booking_id
WHERE u.user_id = :id
GROUP BY u.email;
";
$st = $pdo->prepare($sql);
$st->execute([':id'=>$id]);
$row = $st->fetch();

function h($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Customer Detail</title>
  <style>
    body{font-family:Segoe UI,Arial,sans-serif;background:#FAF8F5;margin:0;color:#333}
    .wrap{max-width:640px;margin:40px auto;background:#fff;padding:24px;border-radius:12px;box-shadow:0 10px 24px rgba(0,0,0,.08)}
    h1{color:#193e65;margin-top:0}
    .field{margin-bottom:10px;font-size:16px}
    .label{font-weight:600;color:#193e65}
    .value{color:#333}
    .btn{display:inline-block;margin-top:16px;text-decoration:none;color:#193e65;border:1px solid #dbe2f2;padding:8px 12px;border-radius:8px;font-weight:700;background:#f5f7fb}
    .btn:hover{background:#eef3fb}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Customer Details</h1>
    <?php if(!$row): ?>
      <p>Customer not found.</p>
    <?php else: ?>
      <div class="field"><span class="label">Email:</span> <span class="value"><?=h($row['email'])?></span></div>
      <div class="field"><span class="label">Total Bookings:</span> <span class="value"><?=h($row['total_bookings'])?></span></div>
      <div class="field"><span class="label">Average Rating:</span> 
        <span class="value">
          <?= $row['avg_rating']!==null ? h(number_format((float)$row['avg_rating'],2)) : '—' ?>
        </span>
      </div>
    <?php endif; ?>
    <p><a class="btn" href="search_customers_form.php">← Back to Customers Search</a></p>
    <p><a class="btn" href="/~nakbogha/admin/index.html">← Back to Admin Hub</a></p>
  </div>
</body>
</html>

