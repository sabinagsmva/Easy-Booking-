<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Search Hotels</title>
  <style>
    body{font-family:Segoe UI,Arial,sans-serif;background:#FAF8F5;margin:0;color:#333}
    .wrap{max-width:640px;margin:40px auto;background:#fff;padding:22px;border-radius:12px;box-shadow:0 10px 24px rgba(0,0,0,.08)}
    label{display:block;margin:10px 0 4px;font-weight:600;color:#193e65}
    input,button{width:100%;padding:12px;border:1px solid #d7dbe0;border-radius:10px;font-size:15px}
    button{margin-top:14px;background:#193e65;color:#fff;border:0;cursor:pointer}
    button:hover{background:#2b5b90}
    .btn{display:inline-block;margin-top:16px;text-decoration:none;color:#193e65;border:1px solid #dbe2f2;padding:8px 12px;border-radius:8px;background:#f5f7fb}
  </style>
</head>
<p style="margin-top:16px">
  <a class="btn" href="/~nakbogha/index.html">← Back to Home</a>
</p>

<body>
  <div class="wrap">
    <h1>Search Hotels by City &amp; Price Range</h1>
    <form action="search_hotels_results.php" method="get">
      <label for="city">City</label>
      <input type="text" id="city" name="city" placeholder="e.g., Bremen" required>

      <label for="min_price">Minimum Price (€)</label>
      <input type="number" id="min_price" name="min_price" min="0" placeholder="e.g., 50">

      <label for="max_price">Maximum Price (€)</label>
      <input type="number" id="max_price" name="max_price" min="0" placeholder="e.g., 300">

      <button type="submit">Search</button>
    </form>
  </div>
</body>
</html
