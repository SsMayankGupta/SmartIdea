-- Dummy Event Registrations for EcoConnect
-- Sample user registrations for sustainability events

-- Insert dummy event registrations
INSERT INTO event_registrations (id, event_id, user_id, full_name, email, phone, attended, points_awarded, registered_at) VALUES
-- Event 1: Clean-Up Drive - India Gate (Event ID 1)
(7001, 1, 9001, 'Rahul Sharma', 'rahul.sharma@email.com', '+91-98765-43210', TRUE, 50, '2025-03-20 10:00:00'),
(7002, 1, 9002, 'Priya Patel', 'priya.patel@email.com', '+91-98765-12345', TRUE, 50, '2025-03-22 14:00:00'),
(7003, 1, 9004, 'Sunita Devi', 'sunita.devi@email.com', '+91-98712-34567', TRUE, 50, '2025-03-25 09:00:00'),
(7004, 1, 9005, 'Vikram Singh', 'vikram.singh@email.com', '+91-98999-88877', TRUE, 50, '2025-03-21 11:00:00'),
(7005, 1, 9008, 'Neha Verma', 'neha.verma@email.com', '+91-97654-32109', TRUE, 50, '2025-03-18 16:00:00'),
(7006, 1, 9010, 'Ananya Roy', 'ananya.roy@email.com', '+91-94321-09876', TRUE, 50, '2025-03-15 09:00:00'),
(7007, 1, 9012, 'Divya Malhotra', 'divya.malhotra@email.com', '+91-92109-87654', FALSE, 0, '2025-03-28 10:00:00'),
(7008, 1, NULL, 'Guest Participant', 'guest1@email.com', '+91-91111-11111', TRUE, 50, '2025-03-25 14:00:00'),

-- Event 2: Tree Plantation - Sector 62 Park (Event ID 2)
(7009, 2, 9002, 'Priya Patel', 'priya.patel@email.com', '+91-98765-12345', TRUE, 75, '2025-03-25 10:00:00'),
(7010, 2, 9004, 'Sunita Devi', 'sunita.devi@email.com', '+91-98712-34567', TRUE, 75, '2025-03-28 09:00:00'),
(7011, 2, 9007, 'Arjun Reddy', 'arjun.reddy@email.com', '+91-96543-21098', TRUE, 75, '2025-03-22 14:00:00'),
(7012, 2, 9008, 'Neha Verma', 'neha.verma@email.com', '+91-97654-32109', TRUE, 75, '2025-03-26 11:00:00'),
(7013, 2, 9010, 'Ananya Roy', 'ananya.roy@email.com', '+91-94321-09876', TRUE, 75, '2025-03-20 08:00:00'),
(7014, 2, 9012, 'Divya Malhotra', 'divya.malhotra@email.com', '+91-92109-87654', TRUE, 75, '2025-03-27 15:00:00'),
(7015, 2, 9015, 'Rohit Malhotra', 'rohit.malhotra@email.com', '+91-99876-54321', TRUE, 75, '2025-03-24 10:30:00'),
(7016, 2, NULL, 'Tree Lover Guest', 'treelover@email.com', '+91-92222-22222', TRUE, 75, '2025-03-29 09:00:00'),

-- Event 3: Recycling Workshop (Event ID 3)
(7017, 3, 9001, 'Rahul Sharma', 'rahul.sharma@email.com', '+91-98765-43210', TRUE, 40, '2025-03-20 09:00:00'),
(7018, 3, 9003, 'Amit Kumar', 'amit.kumar@email.com', '+91-99887-66554', TRUE, 40, '2025-03-22 11:00:00'),
(7019, 3, 9005, 'Vikram Singh', 'vikram.singh@email.com', '+91-98999-88877', TRUE, 40, '2025-03-25 14:00:00'),
(7020, 3, 9006, 'Meera Gupta', 'meera.gupta@email.com', '+91-98123-45678', TRUE, 40, '2025-03-28 10:00:00'),
(7021, 3, 9010, 'Ananya Roy', 'ananya.roy@email.com', '+91-94321-09876', TRUE, 40, '2025-03-18 09:00:00'),
(7022, 3, 9014, 'Pooja Shah', 'pooja.shah@email.com', '+91-90987-65432', TRUE, 40, '2025-03-30 11:00:00'),
(7023, 3, NULL, 'Workshop Enthusiast', 'workshop@email.com', '+91-93333-33333', FALSE, 0, '2025-04-01 10:00:00'),

-- Event 4: E-Waste Collection Drive (Event ID 4)
(7024, 4, 9002, 'Priya Patel', 'priya.patel@email.com', '+91-98765-12345', TRUE, 100, '2025-03-20 10:00:00'),
(7025, 4, 9003, 'Amit Kumar', 'amit.kumar@email.com', '+91-99887-66554', TRUE, 100, '2025-03-22 09:00:00'),
(7026, 4, 9007, 'Arjun Reddy', 'arjun.reddy@email.com', '+91-96543-21098', TRUE, 100, '2025-03-25 14:00:00'),
(7027, 4, 9009, 'Sanjay Kumar', 'sanjay.kumar@email.com', '+91-95432-10987', TRUE, 100, '2025-03-28 11:00:00'),
(7028, 4, 9010, 'Ananya Roy', 'ananya.roy@email.com', '+91-94321-09876', TRUE, 100, '2025-03-18 08:00:00'),
(7029, 4, 9013, 'Karan Mehta', 'karan.mehta@email.com', '+91-91098-76543', TRUE, 100, '2025-03-24 10:00:00'),
(7030, 4, 9015, 'Rohit Malhotra', 'rohit.malhotra@email.com', '+91-99876-54321', TRUE, 100, '2025-03-26 16:00:00'),
(7031, 4, NULL, 'E-Waste Collector', 'ewaste@email.com', '+91-94444-44444', TRUE, 100, '2025-03-29 09:00:00'),
(7032, 4, NULL, 'Tech Recycler', 'tech.recycle@email.com', '+91-95555-55555', TRUE, 100, '2025-03-30 10:00:00'),

-- Additional registrations for past events
(7033, 1, 9007, 'Arjun Reddy', 'arjun.reddy@email.com', '+91-96543-21098', TRUE, 50, '2025-03-20 09:00:00'),
(7034, 2, 9001, 'Rahul Sharma', 'rahul.sharma@email.com', '+91-98765-43210', TRUE, 75, '2025-03-25 10:00:00'),
(7035, 3, 9002, 'Priya Patel', 'priya.patel@email.com', '+91-98765-12345', FALSE, 0, '2025-03-28 14:00:00'),
(7036, 4, 9004, 'Sunita Devi', 'sunita.devi@email.com', '+91-98712-34567', TRUE, 100, '2025-03-22 11:00:00'),
(7037, 1, 9006, 'Meera Gupta', 'meera.gupta@email.com', '+91-98123-45678', FALSE, 0, '2025-03-27 10:00:00'),
(7038, 2, 9003, 'Amit Kumar', 'amit.kumar@email.com', '+91-99887-66554', TRUE, 75, '2025-03-26 09:00:00'),
(7039, 3, 9015, 'Rohit Malhotra', 'rohit.malhotra@email.com', '+91-99876-54321', TRUE, 40, '2025-03-24 11:00:00'),
(7040, 4, 9001, 'Rahul Sharma', 'rahul.sharma@email.com', '+91-98765-43210', TRUE, 100, '2025-03-20 14:00:00')
ON DUPLICATE KEY UPDATE event_id = VALUES(event_id);
