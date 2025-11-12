<?php
require __DIR__ . '/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
  http_response_code(400);
  echo "<p style='font-family:Segoe UI,Arial'>Missing booking id. <a href='search_bookings_form.php'>Back</a></p>";
  exit;
}

/* Derive room_type and payment_kind from your schema */
$sql = "
SELECT 
  b.booking_id, b.check_in, b.check_out, b.status, b.created_at,
  r.room_number, r.price,
  CASE
    WHEN sr.room_id IS NOT NULL THEN 'Single'
    WHEN dr.room_id IS NOT NULL THEN 'Double'
    WHEN su.room_id IS NOT NULL THEN 'Suite'
    ELSE 'Other'
  END AS room_type,
  h.name AS hotel_name, h.address, h.city, h.star_rating,
  u.email AS guest_email,
  -- payment kind + extra columns
  CASE
    WHEN pb.booking_id IS NOT NULL THEN 'Prepaid'
    WHEN pap.booking_id IS NOT NULL THEN 'Pay at Property'
    ELSE '—'
  END AS payment_kind,
  pb.payment_reference,
  pap.payment_method
FROM bookings b
JOIN rooms  r ON b.room_id  = r.room_id
JOIN hotels h ON r.hotel_id = h.hotel_id
JOIN guests g ON b.guest_id = g.user_id         -- matches your FK
JOIN users  u ON g.user_id  = u.user_id
LEFT JOIN single_rooms sr ON r.room_id = sr.room_id
LEFT JOIN double_rooms dr ON r.room_id = dr.room_id
LEFT JOIN suite_rooms  su ON r.room_id = su.room_id
LEFT JOIN prepaid_bookings        pb  ON pb.booking_id = b.booking_id
LEFT JOIN pay_at_property_bookings pap ON pap.booking_id = b.booking_id
WHERE b.booking_id = :id
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$bk = $stmt->fetch();


?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Booking #<?=h($id)?></title>
  <style>
    body{font-family:Segoe UI,Arial,sans-serif;background:#FAF8F5;margin:0;color:#333}
    .wrap{max-width:720px;margin:36px auto;background:#fff;padding:22px;border-radius:12px;box-shadow:0 10px 24px rgba(0,0,0,.08)}
    .row{margin:8px 0}
    .label{color:#6b7280}
    a.btn{display:inline-block;margin-top:10px;text-decoration:none;color:#193e65;border:1px solid #dbe2f2;padding:8px 12px;border-radius:8px;font-weight:700;background:#f5f7fb}
    a.btn:hover{background:#eef3fb}
  </style>
</head>
<body>
  <div class="wrap">
    <?php if (!$bk): ?>
      <h1>Booking not found</h1>
      <p><a class="btn" href="search_bookings_form.php">← Back to search</a></p>
    <?php else: ?>
      <h1>Booking #<?=h($bk['booking_id'])?></h1>
      <div class="row"><span class="label">Guest:</span> <?=h($bk['guest_email'])?></div>
      <div class="row"><span class="label">Hotel:</span> <?=h($bk['hotel_name'])?> (<?=h($bk['city'])?>) · ★<?=h($bk['star_rating'])?></div>
      <div class="row"><span class="label">Room:</span> <?=h($bk['room_number'])?> (<?=h($bk['room_type'])?>) · €<?=h($bk['price'])?></div>
      <div class="row"><span class="label">Dates:</span> <?=h($bk['check_in'])?> → <?=h($bk['check_out'])?> · <span class="label">Status:</span> <?=h($bk['status'])?></div>

      <div class="row">
        <span class="label">Payment:</span> <?=h($bk['payment_kind'])?>
        <?php if ($bk['payment_kind'] === 'Prepaid' && $bk['payment_reference']): ?>
          — Ref: <?=h($bk['payment_reference'])?>
        <?php elseif ($bk['payment_kind'] === 'Pay at Property' && $bk['payment_method']): ?>
          — Method: <?=h($bk['payment_method'])?>
        <?php endif; ?>
      </div>

      <p><a class="btn" href="search_bookings_form.php">← Back</a></p>
    <?php endif; ?>
  </div>
</body>
</html>
