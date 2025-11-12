<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Search Customers</title>
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
<body>
  <div class="wrap">
    <h1>Search Customers</h1>
    <form action="search_customers_results.php" method="get">
      <label for="q">Email contains</label>
      <input type="text" id="q" name="q" placeholder="e.g., @example.com" required>
      <button type="submit">Search</button>
    </form>
    <p><a class="btn" href="/~nakbogha/admin/index.html">← Back to Admin Hub</a></p>
  </div>
</body>
</html>
