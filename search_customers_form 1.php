<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Customer Search (Admin)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/~nakbogha/style.css">
  <style>
    body{font-family:Segoe UI,Arial,sans-serif;background:#FAF8F5;margin:0;color:#333}
    .wrap{max-width:640px;margin:40px auto;background:#fff;padding:24px;border-radius:12px;box-shadow:0 10px 24px rgba(0,0,0,.08)}
    h2{color:#193e65;text-align:center;margin-top:0}
    label{display:block;margin:10px 0 4px;font-weight:600}
    input{width:100%;padding:12px;border:1px solid #d7dbe0;border-radius:8px}
    button{margin-top:16px;background:#193e65;color:#fff;border:0;padding:12px 18px;border-radius:8px;cursor:pointer;font-weight:700}
    button:hover{background:#2b5b90}
    .btn{display:inline-block;margin-top:16px;text-decoration:none;color:#193e65;border:1px solid #dbe2f2;
         padding:8px 12px;border-radius:8px;font-weight:700;background:#f5f7fb}
  </style>
  <link rel="stylesheet"
        href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

  <script src="autocomplete.js"></script>

</head>
<body>
  <header><h2>Search Customers (Admin)</h2></header>
  <main class="wrap">
<form action="search_customers_results.php" method="get">
  <label for="email">Customer Email:</label>
  <input id="email" name="q" placeholder="type email..." required>
  <button type="submit">Search</button>
</form>

    <p><a class="btn" href="/~nakbogha/admin/index.html">← Back to Admin</a></p>
  </main>
</body>
</html>
