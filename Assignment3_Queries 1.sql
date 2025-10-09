USE easybooking;

SELECT h.hotel_id, h.name AS hotel_name, COUNT(sh.user_id) AS staff_count
FROM hotels h
LEFT JOIN staff_hotels sh ON h.hotel_id = sh.hotel_id
GROUP BY h.hotel_id, h.name
ORDER BY staff_count DESC, h.name;

SELECT s.staff_no, u.email, h.name AS hotel_name, sh.role_note
FROM staff s
JOIN users u ON s.user_id = u.user_id
JOIN staff_hotels sh ON s.user_id = sh.user_id
JOIN hotels h ON sh.hotel_id = h.hotel_id
WHERE sh.role_note = 'Manager'
ORDER BY h.name, u.email;

SELECT city, AVG(star_rating) AS avg_stars
FROM hotels
GROUP BY city
HAVING AVG(star_rating) >= 3
ORDER BY avg_stars DESC, city;

SELECT h.hotel_id, h.name AS hotel_name, COUNT(r.room_id) AS available_rooms
FROM hotels h
JOIN rooms r ON h.hotel_id = r.hotel_id
WHERE r.status = 'Available'
GROUP BY h.hotel_id, h.name
ORDER BY available_rooms DESC, h.name;

SELECT h.hotel_id, h.name AS hotel_name,
       CASE
         WHEN sr.room_id IS NOT NULL THEN 'Single'
         WHEN dr.room_id IS NOT NULL THEN 'Double'
         WHEN su.room_id IS NOT NULL THEN 'Suite'
         ELSE 'Other'
       END AS room_type,
       AVG(r.price) AS avg_price
FROM rooms r
JOIN hotels h ON r.hotel_id = h.hotel_id
LEFT JOIN single_rooms sr ON r.room_id = sr.room_id
LEFT JOIN double_rooms dr ON r.room_id = dr.room_id
LEFT JOIN suite_rooms  su ON r.room_id = su.room_id
GROUP BY h.hotel_id, h.name, room_type
HAVING AVG(r.price) > 0
ORDER BY h.name, room_type;

SELECT h.hotel_id, h.name AS hotel_name, COUNT(r.room_id) AS maintenance_rooms
FROM hotels h
JOIN rooms r ON h.hotel_id = r.hotel_id
WHERE r.status = 'Maintenance'
GROUP BY h.hotel_id, h.name
ORDER BY maintenance_rooms DESC, h.name;

SELECT u.email AS guest_email, COUNT(b.booking_id) AS total_bookings
FROM users u
JOIN guests g ON u.user_id = g.user_id
LEFT JOIN bookings b ON g.user_id = b.guest_id
GROUP BY u.email
ORDER BY total_bookings DESC, u.email;

SELECT h.hotel_id, h.name AS hotel_name,
       SUM(DATEDIFF(b.check_out, b.check_in) * r.price) AS total_revenue
FROM hotels h
JOIN rooms r ON h.hotel_id = r.hotel_id
JOIN bookings b ON b.room_id = r.room_id
JOIN prepaid_bookings pb ON pb.booking_id = b.booking_id
GROUP BY h.hotel_id, h.name
ORDER BY total_revenue DESC, h.name;

SELECT h.hotel_id, h.name AS hotel_name,
       AVG(DATEDIFF(b.check_out, b.check_in)) AS avg_nights
FROM hotels h
JOIN rooms r ON h.hotel_id = r.hotel_id
JOIN bookings b ON r.room_id = b.room_id
GROUP BY h.hotel_id, h.name
ORDER BY avg_nights DESC, h.name;

SELECT h.hotel_id, h.name AS hotel_name, AVG(rv.rating) AS avg_rating
FROM reviews rv
JOIN bookings b ON rv.booking_id = b.booking_id
JOIN rooms r ON b.room_id = r.room_id
JOIN hotels h ON r.hotel_id = h.hotel_id
GROUP BY h.hotel_id, h.name
HAVING AVG(rv.rating) >= 4
ORDER BY avg_rating DESC, h.name;

SELECT u.email AS guest_email, rv.rating, rv.comment
FROM reviews rv
JOIN bookings b ON rv.booking_id = b.booking_id
JOIN guests g ON b.guest_id = g.user_id
JOIN users u ON g.user_id = u.user_id
WHERE rv.rating <= 2
ORDER BY rv.rating ASC, u.email;

SELECT YEAR(created_at)  AS year,
       MONTH(created_at) AS month,
       COUNT(booking_id) AS completed_bookings
FROM bookings
WHERE status = 'Completed'
GROUP BY YEAR(created_at), MONTH(created_at)
ORDER BY year, month;