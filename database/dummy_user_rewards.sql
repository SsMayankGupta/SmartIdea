-- Dummy User Rewards Redemptions for EcoConnect
-- Sample reward redemptions by users

-- Insert dummy user reward redemptions
INSERT INTO user_rewards (id, user_id, reward_id, points_spent, status, delivery_address, redeemed_at, delivered_at) VALUES
-- User 9001 - Rahul Sharma (Mixed redemptions)
(6001, 9001, 1, 500, 'delivered', 'Flat 302, Tower B, Supertech Ecovillage, Sector 137, Noida, UP - 201305', '2025-03-01 16:45:00', '2025-03-05 10:00:00'),
(6002, 9001, 4, 200, 'delivered', 'Flat 302, Tower B, Supertech Ecovillage, Sector 137, Noida, UP - 201305', '2025-02-15 11:00:00', '2025-02-20 09:00:00'),
(6003, 9001, 5, 150, 'processing', NULL, '2025-03-25 14:00:00', NULL),

-- User 9002 - Priya Patel (Discount lover)
(6004, 9002, 2, 300, 'delivered', 'House 42, DLF Phase 2, Golf Course Road, Gurugram, HR - 122002', '2025-03-10 10:00:00', '2025-03-12 14:00:00'),
(6005, 9002, 2, 300, 'delivered', 'House 42, DLF Phase 2, Golf Course Road, Gurugram, HR - 122002', '2025-02-15 16:00:00', '2025-02-18 11:00:00'),
(6006, 9002, 4, 200, 'delivered', 'House 42, DLF Phase 2, Golf Course Road, Gurugram, HR - 122002', '2025-03-20 09:30:00', '2025-03-22 10:00:00'),

-- User 9003 - Amit Kumar (High value items)
(6007, 9003, 3, 1000, 'delivered', '12/4, Connaught Place, New Delhi - 110001', '2025-03-20 15:30:00', '2025-03-25 12:00:00'),
(6008, 9003, 6, 750, 'processing', '12/4, Connaught Place, New Delhi - 110001', '2025-04-01 10:00:00', NULL),
(6009, 9003, 5, 150, 'delivered', '12/4, Connaught Place, New Delhi - 110001', '2025-02-20 14:00:00', NULL),

-- User 9004 - Sunita Devi (Service redemptions)
(6010, 9004, 4, 200, 'delivered', 'House 78, Sector 15, Main Market, Faridabad, HR - 121007', '2025-03-25 10:00:00', '2025-03-28 09:00:00'),
(6011, 9004, 5, 150, 'delivered', NULL, '2025-03-15 11:00:00', NULL),

-- User 9005 - Vikram Singh (Tree planter)
(6012, 9005, 5, 150, 'delivered', NULL, '2025-03-22 13:00:00', NULL),
(6013, 9005, 5, 150, 'delivered', NULL, '2025-02-28 10:00:00', NULL),
(6014, 9005, 4, 200, 'pending', 'Tower 15, Jaypee Kosmos, Sector 134, Noida, UP - 201304', '2025-04-01 09:00:00', NULL),

-- User 9006 - Meera Gupta (New redeemer)
(6015, 9006, 5, 150, 'processing', NULL, '2025-03-30 15:00:00', NULL),

-- User 9007 - Arjun Reddy (Merchandise lover)
(6016, 9007, 6, 750, 'delivered', 'Villa 15, Unitech Nirvana Country, Sector 50, Gurugram, HR - 122018', '2025-03-28 11:00:00', '2025-04-01 10:00:00'),
(6017, 9007, 3, 1000, 'processing', 'Villa 15, Unitech Nirvana Country, Sector 50, Gurugram, HR - 122018', '2025-04-02 14:00:00', NULL),

-- User 9008 - Neha Verma (Frequent redeemer)
(6018, 9008, 4, 200, 'delivered', 'Block 12, Karol Bagh, New Delhi - 110005', '2025-03-15 16:00:00', '2025-03-18 09:00:00'),
(6019, 9008, 2, 300, 'delivered', 'Block 12, Karol Bagh, New Delhi - 110005', '2025-02-25 10:00:00', '2025-02-28 14:00:00'),
(6020, 9008, 1, 500, 'delivered', 'Block 12, Karol Bagh, New Delhi - 110005', '2025-01-30 11:00:00', '2025-02-05 10:00:00'),

-- User 9010 - Ananya Roy (Eco Warrior - Heavy redeemer)
(6021, 9010, 3, 1000, 'delivered', 'House 23, Sector 14, Gurugram, HR - 122001', '2025-02-10 12:00:00', '2025-02-15 09:00:00'),
(6022, 9010, 1, 500, 'delivered', 'House 23, Sector 14, Gurugram, HR - 122001', '2025-03-05 15:00:00', '2025-03-08 11:00:00'),
(6023, 9010, 2, 300, 'delivered', 'House 23, Sector 14, Gurugram, HR - 122001', '2025-03-25 11:00:00', '2025-03-28 14:00:00'),
(6024, 9010, 6, 750, 'delivered', 'House 23, Sector 14, Gurugram, HR - 122001', '2025-01-20 10:00:00', '2025-01-25 09:00:00'),
(6025, 9010, 5, 150, 'delivered', NULL, '2025-02-28 16:00:00', NULL),
(6026, 9010, 5, 150, 'delivered', NULL, '2025-03-15 14:00:00', NULL),
(6027, 9010, 4, 200, 'delivered', 'House 23, Sector 14, Gurugram, HR - 122001', '2025-04-01 09:30:00', '2025-04-03 10:00:00'),

-- User 9012 - Divya Malhotra (Pending redemption)
(6028, 9012, 4, 200, 'pending', 'South Extension Part II, New Delhi - 110049', '2025-04-02 16:00:00', NULL),

-- User 9015 - Rohit Malhotra (Gift card lover)
(6029, 9015, 1, 500, 'delivered', 'Flat 402, Sector 31 Apartments, Gurugram, HR - 122003', '2025-03-10 14:00:00', '2025-03-15 11:00:00'),
(6030, 9015, 2, 300, 'delivered', 'Flat 402, Sector 31 Apartments, Gurugram, HR - 122003', '2025-02-20 10:00:00', '2025-02-25 09:00:00'),
(6031, 9015, 5, 150, 'delivered', NULL, '2025-03-28 11:30:00', NULL)
ON DUPLICATE KEY UPDATE user_id = VALUES(user_id);
