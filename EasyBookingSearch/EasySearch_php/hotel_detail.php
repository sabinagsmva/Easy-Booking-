<?php
ini_set('display_errors',1); error_reporting(E_ALL);
require __DIR__.'/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) exit('Missing hotel id. <a href="search_hotel_form.php">Back</a>');

$h = $pdo->prepare("SELECT hotel_id, name, address, city, star_rating, status
                    FROM hotels WHERE hotel_id = :id");
$h->execute([':id'=>$id]);
$hotel = $h->fetch();

$r = $pdo->prepare("SELECT room_id, room_number, price, status
                    FROM rooms WHERE hotel_id = :id ORDER BY room_number");
$r->execute([':id'=>$id]);
$rooms = $r->fetchAll();

function h($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Hotel Details</title>
  <style>
    body{font-family:Segoe UI,Arial,sans-serif;background:#FAF8F5;margin:0;color:#333}
    .wrap{max-width:720px;margin:40px auto;background:#fff;padding:24px;border-radius:12px;box-shadow:0 10px 24px rgba(0,0,0,.08)}
    h1{color:#193e65;margin-top:0}
    .muted{color:#6b7280}
    table{width:100%;border-collapse:collapse;margin-top:16px}
    th,td{border-bottom:1px solid #e5e7eb;padding:10px;text-align:left}
    th{background:#193e65;color:#fff}
    .btn{display:inline-block;margin-top:16px;text-decoration:none;color:#193e65;border:1px solid #dbe2f2;padding:8px 12px;border-radius:8px;font-weight:700;background:#f5f7fb}
    .btn:hover{background:#eef3fb}
  </style>
</head>
<p style="margin-top:16px">
  <a class="btn" href="/~nakbogha/index.html">← Back to Home</a>
</p>

<body>
  <div class="wrap">
    <h1>Hotel Details</h1>
    <?php if (!$hotel):
