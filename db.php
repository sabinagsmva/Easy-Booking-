<?php
try {
  $pdo = new PDO(
    'mysql:host=127.0.0.1;port=3306;dbname=db_nakhogha;charset=utf8mb4',
    'nakhogha',
    'bQKetWesJEgLzgIn',
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
  );
} catch (PDOException $e) {
  die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}
if (!function_exists('h')) {
  function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}
