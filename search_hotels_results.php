<?php
ini_set('display_errors',1); error_reporting(E_ALL);
require __DIR__.'/db.php';

$city = trim($_GET['city'] ?? '');
$min  = (float)($_GET['min_price'] ?? 0);
$max  = (float)($_GET['max_price'] ?? 999999);
if ($city === '') die('City is required. <a href="search_hotel_form.php">Back</a>');

$sql = "
SELECT h.hotel_id, h.name AS hotel_name, h.city, h.star_rating,
       MIN(r.price) AS min_price, MAX(r.price) AS max_price, COUNT(r.room_id) AS room_count
FROM hotels h
JOIN rooms r ON r.hotel_id = h.hotel_id
WHERE h.city LIKE :city AND r.price BETWEEN :min AND :max
GROUP BY h.hotel_id, h.name, h.city, h.star_rating
ORDER BY min_price ASC, h.name ASC;
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':city'=>"%$city%", ':min'=>$min, ':max'=>$max]);
$rows = $stmt->fetchAll();

function h($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Hotel Search Results</title>
  <style>
    body{font-family:Segoe UI,Arial,sans-serif;background:#FAF8F5;margin:0;color:#333}
    .wrap{max-width:960px;margin:34px auto;padding:0 16px}
    .card{background:#fff;border:1px solid #e7e9ee;border-radius:12px;padding:14px 16px;margin:10px 0;box-shadow:0 6px 16px rgba(0,0,0,.06)}
    .muted{color:#6b7280}
    .btn{display:inline-block;margin-top:8px;text-decoration:none;color:#193e65;border:1px solid #dbe2f2;padding:8px 12px;border-radius:8px;font-weight:700;background:#f5f7fb}
    .btn:hover{background:#eef3fb}
  </style>
</head>
<p style="margin-top:16px">
  <a class="btn" href="/~nakbogha/index.html">← Back to Home</a>
</p>

<body>
  <div class="wrap">
    <h1>Hotels in “<?=h($city)?>” (€<?=h($min)?>–<?=h($max)?>)</h1>
    <?php if (!$rows): ?>
      <p>No hotels found.</p>
      <p><a class="btn" href="search_hotel_form.php">← New search</a></p>
    <?php else: foreach ($rows as $r): ?>
      <div class="card">
        <strong><?=h($r['hotel_name'])?></strong> (<?=h($r['city'])?>) ★<?=h($r['star_rating'])?><br>
        Min €<?=h(number_format((float)$r['min_price'],2))?> – Max €<?=h(number_format((float)$r['max_price'],2))?><br>
        Rooms: <?=h($r['room_count'])?><br>
        <a class="btn" href="hotel_detail.php?id=<?=h($r['hotel_id'])?>">View details</a>
      </div>
    <?php endforeach; endif; ?>
    <p><a class="btn" href="search_hotel_form.php">← Back</a></p>
  </div>
</body>
</html>
