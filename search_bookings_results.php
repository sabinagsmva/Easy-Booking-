<?php
// TEMP: show errors on screen while debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/db.php'; // uses $pdo (PDO) with CLAMV creds

$from  = $_GET['from']  ?? '';
$to    = $_GET['to']    ?? '';
$rtype = $_GET['rtype'] ?? ''; // '', 'Single', 'Double', 'Suite'

if (!$from || !$to) {
  http_response_code(400);
  exit("Missing date range. <a href='search_bookings_form.php'>Back</a>");
}

// Use OVERLAP logic so any booking intersecting the range appears:
$sql = "
SELECT 
  b.booking_id, b.check_in, b.check_out, b.status,
  r.room_number, r.price,
  CASE
    WHEN sr.room_id IS NOT NULL THEN 'Single'
    WHEN dr.room_id IS NOT NULL THEN 'Double'
    WHEN su.room_id IS NOT NULL THEN 'Suite'
    ELSE 'Other'
  END AS room_type,
  h.name AS hotel_name,
  u.email AS guest_email,
  CASE
    WHEN pb.booking_id IS NOT NULL THEN 'Prepaid'
    WHEN pap.booking_id IS NOT NULL THEN 'Pay at Property'
    ELSE '—'
  END AS payment_kind
FROM bookings b
JOIN rooms  r ON b.room_id  = r.room_id
JOIN hotels h ON r.hotel_id = h.hotel_id
JOIN guests g ON b.guest_id = g.user_id         -- your FK: bookings.guest_id -> guests(user_id)
JOIN users  u ON g.user_id  = u.user_id
LEFT JOIN single_rooms sr ON r.room_id = sr.room_id
LEFT JOIN double_rooms dr ON r.room_id = dr.room_id
LEFT JOIN suite_rooms  su ON r.room_id = su.room_id
LEFT JOIN prepaid_bookings        pb  ON pb.booking_id = b.booking_id
LEFT JOIN pay_at_property_bookings pap ON pap.booking_id = b.booking_id
WHERE b.check_in  <= :to
  AND b.check_out >= :from
  AND (
        :rtype = '' 
        OR (:rtype = 'Single' AND sr.room_id IS NOT NULL)
        OR (:rtype = 'Double' AND dr.room_id IS NOT NULL)
        OR (:rtype = 'Suite'  AND su.room_id IS NOT NULL)
      )
ORDER BY b.check_in DESC, b.booking_id DESC
";

try {
  $st = $pdo->prepare($sql);
  $st->execute([
    ':from'  => $from,
    ':to'    => $to,
    ':rtype' => $rtype
  ]);
  $rows = $st->fetchAll();
} catch (Throwable $e) {
  // If something is wrong (bad column/table), show it instead of HTTP 500
  exit('Query failed: ' . htmlspecialchars($e->getMessage()) . " <br><a href='search_bookings_form.php'>Back</a>");
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Booking Results</title>
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
    <h1>Bookings from <?=h($from)?> to <?=h($to)?> <?= $rtype ? "· Room Type: ".h($rtype) : "" ?></h1>

    <?php if (!$rows): ?>
      <p>No bookings found for this range/type.</p>
      <p><a class="btn" href="search_bookings_form.php">← New search</a></p>
    <?php else: foreach ($rows as $b): ?>
      <div class="card">
        <strong>#<?=h($b['booking_id'])?></strong> — <?=h($b['check_in'])?> → <?=h($b['check_out'])?> ·
        <span class="muted"><?=h($b['status'])?> · <?=h($b['payment_kind'])?></span><br>
        Room <?=h($b['room_number'])?> (<?=h($b['room_type'])?>) @ <?=h($b['hotel_name'])?> · €<?=h(number_format((float)$b['price'],2))?><br>
        Guest: <?=h($b['guest_email'])?><br>
        <a class="btn" href="booking_detail.php?id=<?=h($b['booking_id'])?>">View details</a>
      </div>
    <?php endforeach; endif; ?>

    <p><a class="btn" href="search_bookings_form.php">← Back</a></p>
    <p><a class="btn" href="/~nakbogha/admin/index.html">← Back to Admin Hub</a></p>
  </div>
</body>
</html>
