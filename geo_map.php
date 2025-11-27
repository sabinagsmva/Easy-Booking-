<?php

$use_api = 'ip-api';
$ipinfo_token = '';
$cache_dir = __DIR__ . '/geo_cache';
$cache_ttl = 60 * 60;

function client_ip() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($parts[0]);
    }
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

function cache_get($key, $cache_dir, $ttl) {
    $fn = $cache_dir . '/' . preg_replace('/[^a-z0-9_.-]/i','_', $key) . '.json';
    if (!file_exists($fn)) return null;
    if (time() - filemtime($fn) > $ttl) return null;
    $c = file_get_contents($fn);
    return json_decode($c, true);
}
function cache_set($key, $data, $cache_dir) {
    if (!is_dir($cache_dir)) @mkdir($cache_dir, 0700, true);
    $fn = $cache_dir . '/' . preg_replace('/[^a-z0-9_.-]/i','_', $key) . '.json';
    file_put_contents($fn, json_encode($data));
}

$ip = client_ip();

if (!empty($_GET['ip'])) $ip = trim($_GET['ip']);

$cache_key = "geo_{$ip}";
$geo = cache_get($cache_key, $cache_dir, $cache_ttl);

if (!$geo) {
    if ($use_api === 'ip-api') {
        $url = "http://ip-api.com/json/" . urlencode($ip) . "?fields=status,message,country,regionName,city,lat,lon,query";
        $json = @file_get_contents($url);
        $dat = $json ? json_decode($json, true) : null;
        if (!$dat || ($dat['status'] ?? '') !== 'success') {
            $geo = ['ok'=>false, 'error'=>$dat['message'] ?? 'lookup-failed'];
        } else {
            $geo = ['ok'=>true,
                    'ip'=>$dat['query'],
                    'lat'=>$dat['lat'] + 0.0,
                    'lon'=>$dat['lon'] + 0.0,
                    'city'=>$dat['city'] ?? '',
                    'region'=>$dat['regionName'] ?? '',
                    'country'=>$dat['country'] ?? ''
            ];
            cache_set($cache_key, $geo, $cache_dir);
        }
    } else {
        $token = trim($ipinfo_token);
        $u = "https://ipinfo.io/{$ip}/json" . ($token ? "?token=" . rawurlencode($token) : "");
        $json = @file_get_contents($u);
        $dat = $json ? json_decode($json, true) : null;
        if (!$dat || empty($dat['loc'])) {
            $geo = ['ok'=>false, 'error'=>'lookup-failed'];
        } else {
            list($lat, $lon) = explode(',', $dat['loc']);
            $geo = ['ok'=>true,
                    'ip'=>$dat['ip'] ?? $ip,
                    'lat'=>floatval($lat),
                    'lon'=>floatval($lon),
                    'city'=>$dat['city'] ?? '',
                    'region'=>$dat['region'] ?? '',
                    'country'=>$dat['country'] ?? ''
            ];
            cache_set($cache_key, $geo, $cache_dir);
        }
    }
}

if (empty($geo['ok'])) {
    $fallback_lat = 51.509865;
    $fallback_lon = -0.118092;
    $geo = [
        'ok'=>false,
        'error'=>$geo['error'] ?? 'lookup failed',
        'ip'=>$ip,
        'lat'=>$fallback_lat,
        'lon'=>$fallback_lon,
        'city'=>'Unknown',
        'region'=>'',
        'country'=>''
    ];
}

?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Your region (geo lookup)</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
  body{font-family:Segoe UI,Arial,sans-serif;margin:0}
  header{background:#193e65;color:#fff;padding:12px 16px}
  main{padding:16px}
  #map{height:60vh;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,.08)}
  .meta{margin:12px 0}
  .hint{color:#666;font-size:0.95rem}
  .btn{display:inline-block;background:#f5f7fb;color:#193e65;padding:8px 12px;border-radius:8px;text-decoration:none;border:1px solid #e3e7f2}
</style>
</head>
<body>
<header>
  <h1>Where are you?</h1>
</header>

<main>
  <div class="meta">
    <strong>IP:</strong> <?=htmlspecialchars($geo['ip'])?> &nbsp; 
    <strong>Location:</strong> <?=htmlspecialchars(trim($geo['city'] . ', ' . $geo['region'] . ', ' . $geo['country']))?>
    <?php if (!$geo['ok']): ?>
      <div class="hint">Lookup failed: <?=htmlspecialchars($geo['error'])?> — showing fallback coordinates.</div>
    <?php endif ?>
  </div>

  <div id="map"></div>

  <p style="margin-top:12px">
    <a class="btn" href="/~nakbogha/index.html">← Back to Home</a>
    <a class="btn" href="/~nakbogha/admin/index.html">Admin</a>
  </p>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  (function(){
    var lat = <?= json_encode($geo['lat']) ?>;
    var lon = <?= json_encode($geo['lon']) ?>;
    var map = L.map('map').setView([lat, lon], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([lat, lon]).addTo(map);
    marker.bindPopup("<b>IP: <?= addslashes(htmlspecialchars($geo['ip'])) ?></b><br>"
                     + "<?= addslashes(htmlspecialchars(trim($geo['city'] . ', ' . $geo['region'] . ', ' . $geo['country']))) ?>")
          .openPopup();

    setTimeout(function(){ map.invalidateSize(); }, 300);
  })();
</script>
</body>
</html>
