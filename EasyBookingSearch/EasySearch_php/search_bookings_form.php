<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Search Bookings</title>
  <style>
    body{font-family:Segoe UI,Arial,sans-serif;background:#FAF8F5;margin:0;color:#333}
    .wrap{max-width:640px;margin:40px auto;background:#fff;padding:22px;border-radius:12px;box-shadow:0 10px 24px rgba(0,0,0,.08)}
    label{display:block;margin:10px 0 4px;font-weight:600;color:#193e65}
    input,select,button{width:100%;padding:12px;border:1px solid #d7dbe0;border-radius:10px;font-size:15px}
    button{margin-top:14px;background:#193e65;color:#fff;border:0;cursor:pointer}
    button:hover{background:#2b5b90}
    .btn{display:inline-block;margin-top:16px;text-decoration:none;color:#193e65;border:1px solid #dbe2f2;padding:8px 12px;border-radius:8px;background:#f5f7fb}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Search Bookings by Date &amp; Room Type</h1>

    <form action="search_bookings_results.php" method="get">
      <label for="from">From (check-in)</label>
      <input type="date" id="from" name="from" required>

      <label for="to">To (check-out)</label>
      <input type="date" id="to" name="to" required>

      <label for="rtype">Room Type</label>
      <select id="rtype" name="rtype">
        <option value="">Any</option>
        <option value="Single">Single</option>
        <option value="Double">Double</option>
        <option value="Suite">Suite</option>
      </select>

      <button type="submit">Search</button>
    </form>

    <p><a class="btn" href="/~nakbogha/admin/index.html">← Back to Admin Hub</a></p>
  </div>
</body>
</html>
