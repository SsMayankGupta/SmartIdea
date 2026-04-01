-- Dummy Users Data for EcoConnect
-- This file contains sample user accounts for testing

-- Clear existing dummy users first (optional - remove if not needed)
-- DELETE FROM users WHERE id BETWEEN 9000 AND 9999;

-- Insert dummy users
INSERT INTO users (id, full_name, email, password_hash, phone, city, area, user_type, green_points, level, is_active, email_verified, created_at) VALUES
(9001, 'Rahul Sharma', 'rahul.sharma@email.com', '$2y$10$dummyhash123', '+91-98765-43210', 'Noida', 'Sector 62', 'resident', 1250, 'Sapling', TRUE, TRUE, '2025-01-15 10:30:00'),
(9002, 'Priya Patel', 'priya.patel@email.com', '$2y$10$dummyhash124', '+91-98765-12345', 'Gurugram', 'DLF Phase 2', 'resident', 850, 'Sprout', TRUE, TRUE, '2025-02-20 14:15:00'),
(9003, 'Amit Kumar', 'amit.kumar@email.com', '$2y$10$dummyhash125', '+91-99887-66554', 'Delhi', 'Connaught Place', 'commercial', 2100, 'Tree', TRUE, TRUE, '2025-01-05 09:00:00'),
(9004, 'Sunita Devi', 'sunita.devi@email.com', '$2y$10$dummyhash126', '+91-98712-34567', 'Faridabad', 'Sector 15', 'resident', 450, 'Seedling', TRUE, TRUE, '2025-03-10 16:45:00'),
(9005, 'Vikram Singh', 'vikram.singh@email.com', '$2y$10$dummyhash127', '+91-98999-88877', 'Noida', 'Sector 134', 'resident', 675, 'Sprout', TRUE, TRUE, '2025-02-28 11:20:00'),
(9006, 'Meera Gupta', 'meera.gupta@email.com', '$2y$10$dummyhash128', '+91-98123-45678', 'Delhi', 'Rajouri Garden', 'resident', 320, 'Seedling', TRUE, TRUE, '2025-03-15 13:30:00'),
(9007, 'Arjun Reddy', 'arjun.reddy@email.com', '$2y$10$dummyhash129', '+91-96543-21098', 'Gurugram', 'Sector 50', 'commercial', 1580, 'Sapling', TRUE, TRUE, '2025-01-25 08:45:00'),
(9008, 'Neha Verma', 'neha.verma@email.com', '$2y$10$dummyhash130', '+91-97654-32109', 'Delhi', 'Karol Bagh', 'resident', 920, 'Sprout', TRUE, TRUE, '2025-02-14 15:00:00'),
(9009, 'Sanjay Kumar', 'sanjay.kumar@email.com', '$2y$10$dummyhash131', '+91-95432-10987', 'Noida', 'Sector 18', 'resident', 145, 'Seedling', FALSE, TRUE, '2025-03-20 10:00:00'),
(9010, 'Ananya Roy', 'ananya.roy@email.com', '$2y$10$dummyhash132', '+91-94321-09876', 'Gurugram', 'Sector 14', 'resident', 2340, 'Eco Warrior', TRUE, TRUE, '2024-12-01 09:30:00'),
(9011, 'Rajesh Khanna', 'rajesh.khanna@email.com', '$2y$10$dummyhash133', '+91-93210-98765', 'Faridabad', 'Sector 21', 'admin', 5000, 'Eco Warrior', TRUE, TRUE, '2024-11-15 08:00:00'),
(9012, 'Divya Malhotra', 'divya.malhotra@email.com', '$2y$10$dummyhash134', '+91-92109-87654', 'Delhi', 'South Extension', 'resident', 780, 'Sprout', TRUE, TRUE, '2025-02-08 17:15:00'),
(9013, 'Karan Mehta', 'karan.mehta@email.com', '$2y$10$dummyhash135', '+91-91098-76543', 'Noida', 'Sector 50', 'commercial', 1890, 'Tree', TRUE, TRUE, '2025-01-10 12:00:00'),
(9014, 'Pooja Shah', 'pooja.shah@email.com', '$2y$10$dummyhash136', '+91-90987-65432', 'Delhi', 'Lajpat Nagar', 'resident', 560, 'Sprout', TRUE, FALSE, '2025-03-25 14:30:00'),
(9015, 'Rohit Malhotra', 'rohit.malhotra@email.com', '$2y$10$dummyhash137', '+91-99876-54321', 'Gurugram', 'Sector 31', 'resident', 1340, 'Sapling', TRUE, TRUE, '2025-01-20 16:00:00')
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

-- Note: Passwords are hashed placeholders. In production, use proper password hashing.
