<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Find a Booking</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="/~nakbogha/style.css" />
  <style>
    body{font-family:Segoe UI,Arial,sans-serif;background:#FAF8F5;margin:0;color:#333}
    .wrap{max-width:680px;margin:34px auto;background:#fff;padding:22px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,.06)}
    label{display:block;margin:10px 0 6px;font-weight:600}
    input,select{width:100%;padding:10px;border:1px solid #d7dbe0;border-radius:8px}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    button{margin-top:14px;background:#193e65;color:#fff;border:0;padding:12px;border-radius:8px;cursor:pointer}
  </style>
  <link rel="stylesheet"
        href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

  <script src="autocomplete.js"></script>

</head>
<body>
  <header><h2 style="text-align:center;margin:24px 0">Search Bookings (Admin)</h2></header>

  <main class="wrap">
    <form action="search_result.php" method="get">
      <div class="row">
        <div>
          <label>From (check-in on/after)</label>
          <input type="date" name="from" />
        </div>
        <div>
          <label>To (check-out on/before)</label>
          <input type="date" name="to" />
        </div>
      </div>

      <label>Room type (optional)</label>
      <select name="rtype">
        <option value="">Any</option>
        <option>Single</option>
        <option>Double</option>
        <option>Suite</option>
      </select>
     <label for="email">Guest Email (optional)</label>
    <input type="text" id="email" name="email" placeholder="guest1@example.com">

      <button type="submit">Search</button>
    </form>

    <p style="margin-top:16px">
      <a class="btn" href="/~nakbogha/admin/index.html">← Back to Admin</a>
    </p>
  </main>
</body>
</html>
