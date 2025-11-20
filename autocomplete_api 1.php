<?php

header('Content-Type: application/json');


$host = "localhost";
$user = "milicatadic";     // example username
$pass = "REDACTED";        // replace with your DB password
$db   = "easybooking";     // your database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}


$term  = isset($_GET['term']) ? $_GET['term'] : "";
$field = isset($_GET['field']) ? $_GET['field'] : "";

$data = [];


switch ($field) {

    case "city":
        $stmt = $conn->prepare("
            SELECT DISTINCT city 
            FROM hotels 
            WHERE city LIKE CONCAT('%', ?, '%')
            ORDER BY city ASC
        ");
        $stmt->bind_param("s", $term);
        break;

    case "hotel_name":
        $stmt = $conn->prepare("
            SELECT DISTINCT name 
            FROM hotels 
            WHERE name LIKE CONCAT('%', ?, '%')
            ORDER BY name ASC
        ");
        $stmt->bind_param("s", $term);
        break;

    case "customer_name":
        $stmt = $conn->prepare("
            SELECT DISTINCT full_name 
            FROM customers 
            WHERE full_name LIKE CONCAT('%', ?, '%')
            ORDER BY full_name ASC
        ");
        $stmt->bind_param("s", $term);
        break;

    case "email":
        $stmt = $conn->prepare("
            SELECT DISTINCT email 
            FROM customers 
            WHERE email LIKE CONCAT('%', ?, '%')
            ORDER BY email ASC
        ");
        $stmt->bind_param("s", $term);
        break;

    case "room_type":
        $stmt = $conn->prepare("
            SELECT DISTINCT room_type 
            FROM bookings 
            WHERE room_type LIKE CONCAT('%', ?, '%')
            ORDER BY room_type ASC
        ");
        $stmt->bind_param("s", $term);
        break;

    default:
        echo json_encode([]);
        exit;
}


$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_array()) {
    $data[] = $row[0];
}

$stmt->close();
$conn->close();


echo json_encode($data);
?>
