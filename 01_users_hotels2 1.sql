CREATE DATABASE IF NOT EXISTS easybooking;
USE easybooking;

-- Drop in dependency order (safe re-run)
DROP TABLE IF EXISTS reviews,
                     prepaid_bookings,
                     pay_at_property_bookings,
                     bookings,
                     suite_rooms,
                     double_rooms,
                     single_rooms,
                     rooms,
                     staff_hotels,
                     hotels,
                     staff,
                     guests,
                     users;

CREATE TABLE users (
  user_id        BIGINT PRIMARY KEY AUTO_INCREMENT,
  email          VARCHAR(255) NOT NULL UNIQUE,
  password_hash  VARCHAR(255) NOT NULL,
  role           ENUM('Guest','Staff') NOT NULL,
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE guests (
  user_id BIGINT PRIMARY KEY,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE staff (
  user_id  BIGINT PRIMARY KEY,
  staff_no VARCHAR(50) UNIQUE,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE hotels (
  hotel_id    BIGINT PRIMARY KEY AUTO_INCREMENT,
  name        VARCHAR(200) NOT NULL,
  address     VARCHAR(300),
  city        VARCHAR(120),
  star_rating TINYINT,
  status      ENUM('Active','Suspended') NOT NULL DEFAULT 'Active'
);

CREATE TABLE staff_hotels (
  user_id   BIGINT NOT NULL,
  hotel_id  BIGINT NOT NULL,
  role_note VARCHAR(120),
  PRIMARY KEY (user_id, hotel_id),
  FOREIGN KEY (user_id)  REFERENCES staff(user_id)   ON DELETE CASCADE,
  FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE
);

CREATE TABLE rooms (
  room_id     BIGINT PRIMARY KEY AUTO_INCREMENT,
  hotel_id    BIGINT NOT NULL,
  room_number VARCHAR(50) NOT NULL,
  price       DECIMAL(10,2) NOT NULL,
  status      ENUM('Available','Occupied','Maintenance') NOT NULL DEFAULT 'Available',
  UNIQUE (hotel_id, room_number),
  FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE
);

CREATE TABLE single_rooms (
  room_id  BIGINT PRIMARY KEY,
  bed_size ENUM('Twin','Queen','King') NOT NULL,
  FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

CREATE TABLE double_rooms (
  room_id       BIGINT PRIMARY KEY,
  has_extra_bed BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

CREATE TABLE suite_rooms (
  room_id         BIGINT PRIMARY KEY,
  has_living_area BOOLEAN NOT NULL DEFAULT TRUE,
  FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

CREATE TABLE bookings (
  booking_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  guest_id   BIGINT NOT NULL,
  room_id    BIGINT NOT NULL,
  check_in   DATE NOT NULL,
  check_out  DATE NOT NULL,
  status     ENUM('Pending','Confirmed','Cancelled','Completed') NOT NULL DEFAULT 'Pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (guest_id) REFERENCES guests(user_id) ON DELETE CASCADE,
  FOREIGN KEY (room_id)  REFERENCES rooms(room_id)  ON DELETE CASCADE
);

CREATE TABLE prepaid_bookings (
  booking_id        BIGINT PRIMARY KEY,
  payment_reference VARCHAR(100) NOT NULL,
  FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
);

CREATE TABLE pay_at_property_bookings (
  booking_id     BIGINT PRIMARY KEY,
  payment_method ENUM('Cash','Card') NOT NULL,
  FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
);

CREATE TABLE reviews (
  review_id  BIGINT PRIMARY KEY AUTO_INCREMENT,
  booking_id BIGINT NOT NULL,
  rating     TINYINT NOT NULL,
  comment    TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE,
  CHECK (rating BETWEEN 1 AND 5)
);

INSERT INTO users (email, password_hash, role) VALUES
('guest1@example.com','x','Guest'),
('guest2@example.com','x','Guest'),
('staff1@example.com','x','Staff'),
('staff2@example.com','x','Staff');

INSERT INTO guests (user_id) VALUES (1),(2);
INSERT INTO staff  (user_id, staff_no) VALUES (3,'STF-001'),(4,'STF-002');

INSERT INTO hotels (name, address, city, star_rating, status) VALUES
('EasyBooking Hotel','Street 1','Bremen',4,'Active'),
('Seaside Resort','Ocean Drive 12','Hamburg',5,'Active');

INSERT INTO staff_hotels (user_id, hotel_id, role_note) VALUES
(3,1,'Manager'),
(4,2,'Manager');

INSERT INTO rooms (hotel_id, room_number, price, status) VALUES
(1,'101', 80.00,'Available'),
(1,'102',120.00,'Available'),
(1,'201',200.00,'Maintenance');

INSERT INTO rooms (hotel_id, room_number, price, status) VALUES
(2,'101',150.00,'Available'),
(2,'102',250.00,'Available');

INSERT INTO single_rooms (room_id, bed_size) VALUES (1,'Queen'),(4,'Queen');
INSERT INTO double_rooms (room_id, has_extra_bed) VALUES (2,TRUE),(5,TRUE);
INSERT INTO suite_rooms  (room_id, has_living_area) VALUES (3,TRUE);

INSERT INTO bookings (guest_id, room_id, check_in, check_out, status, created_at) VALUES
(1,1,'2025-10-01','2025-10-05','Completed','2025-10-01 10:00:00'),
(1,2,'2025-11-10','2025-11-12','Confirmed','2025-11-01 09:00:00');
-- Hotel 2 (guest2)
INSERT INTO bookings (guest_id, room_id, check_in, check_out, status, created_at) VALUES
(2,4,'2025-09-01','2025-09-04','Completed','2025-09-01 09:00:00'),
(2,5,'2025-10-10','2025-10-14','Completed','2025-10-10 09:00:00');

INSERT INTO prepaid_bookings (booking_id, payment_reference) VALUES
(1,'TXN-EB-001'),
(3,'TXN-SEA-101'),
(4,'TXN-SEA-102');

INSERT INTO pay_at_property_bookings (booking_id, payment_method) VALUES
(2,'Cash');

INSERT INTO reviews (booking_id, rating, comment, created_at) VALUES
(1,5,'Excellent stay!','2025-10-06 11:00:00'),
(2,2,'Noisy AC','2025-11-12 10:00:00'),
(3,4,'Great food and view!','2025-09-05 10:00:00'),
(4,5,'Amazing stay!','2025-10-15 12:00:00');
