<?php


function getClientIp(): string {
    $keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];

    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ips = explode(',', $_SERVER[$key]);
            foreach ($ips as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    }
    return '0.0.0.0';
}

$clientIp = getClientIp();

$lat = 53.0833;   // default: Bremen (fallback)
$lon = 8.8000;
$city = "Unknown";
$region = "";
$country = "";

$apiUrl = "http://ip-api.com/json/" . urlencode($clientIp);

$geoJson = @file_get_contents($apiUrl);
if ($geoJson !== false) {
    $geo = json_decode($geoJson, true);
    if (is_array($geo) && isset($geo['status']) && $geo['status'] === 'success') {
        $lat     = $geo['lat'];
        $lon     = $geo['lon'];
        $city    = $geo['city'] ?? "Unknown";
        $region  = $geo['regionName'] ?? "";
        $country = $geo['country'] ?? "";
    }
}


$clientIpJs = json_encode($clientIp);
$latJs      = json_encode($lat);
$lonJs      = json_encode($lon);
$cityJs     = json_encode($city);
$regionJs   = json_encode($region);
$countryJs  = json_encode($country);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IP Location Map – Assignment 10</title>


    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-sA+4J8EeU86Z3D1p5gA9Uc1NQFvC4KpQrQuYf0SYJ7g="
        crossorigin=""
    />

    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 20px 24px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.08);
        }
        h1 {
            margin-top: 0;
            font-size: 1.8rem;
        }
        #map {
            width: 100%;
            height: 450px;
            border-radius: 10px;
            border: 1px solid #ddd;
            margin-top: 16px;
        }
        .info-box {
            padding: 10px 14px;
            background: #f0f4ff;
            border-radius: 8px;
            margin-bottom: 12px;
            border: 1px solid #c3d4ff;
            font-size: 0.95rem;
        }
        .info-label {
            font-weight: 600;
        }
        footer {
            margin-top: 18px;
            font-size: 0.8rem;
            color: #777;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Client IP Location Map</h1>

    <div class="info-box">
        <div><span class="info-label">Your IP:</span> <?php echo htmlspecialchars($clientIp); ?></div>
        <div><span class="info-label">Approximate Location:</span>
            <?php
            $parts = array_filter([$city, $region, $country]);
            echo htmlspecialchars($parts ? implode(', ', $parts) : 'Unknown');
            ?>
        </div>
        <div><span class="info-label">Coordinates:</span>
            <?php echo htmlspecialchars($lat . ", " . $lon); ?>
        </div>
    </div>

    <p>
        The map below is generated using <strong>Leaflet</strong> with an
        <strong>OpenStreetMap</strong> layer. The marker shows the approximate
        location corresponding to your IP address, based on a geo-lookup service.
    </p>

    <div id="map"></div>

    <footer>
        Databases Project 2025 – Assignment 10 · Linked Services (IP → Geolocation → Map)
    </footer>
</div>


<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-o9N1j7kGStb6tKzHnF8zSgFpU0uZsD2U5xM5p3p3p3s="
    crossorigin="">
</script>

<script>
    const clientIp = <?php echo $clientIpJs; ?>;
    const lat      = <?php echo $latJs; ?>;
    const lon      = <?php echo $lonJs; ?>;
    const city     = <?php echo $cityJs; ?>;
    const region   = <?php echo $regionJs; ?>;
    const country  = <?php echo $countryJs; ?>;

    const map = L.map('map').setView([lat, lon], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const locationTextParts = [];
    if (city)    locationTextParts.push(city);
    if (region)  locationTextParts.push(region);
    if (country) locationTextParts.push(country);

    const locationText = locationTextParts.join(', ') || 'Unknown location';

    const marker = L.marker([lat, lon]).addTo(map);
    marker.bindPopup(
        "<strong>Your IP:</strong> " + clientIp + "<br>" +
        "<strong>Location:</strong> " + locationText + "<br>" +
        "<strong>Lat/Lon:</strong> " + lat + ", " + lon
    ).openPopup();
</script>

</body>
</html>
