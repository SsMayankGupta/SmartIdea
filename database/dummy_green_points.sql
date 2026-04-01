-- Dummy Green Points Transactions for EcoConnect
-- Sample transaction history for users

-- Insert dummy green points transactions
INSERT INTO green_points_transactions (id, user_id, transaction_type, points, description, reference_id, reference_type, expiry_date, created_at) VALUES
-- User 9001 - Rahul Sharma
(2001, 9001, 'earned', 50, 'Waste pickup completed - Plastic bottles', 1001, 'waste_report', '2026-03-15', '2025-03-15 14:00:00'),
(2002, 9001, 'earned', 100, 'Quiz completion - Perfect score', NULL, 'quiz', NULL, '2025-02-20 10:30:00'),
(2003, 9001, 'redeemed', -500, 'Redeemed Amazon Gift Card ₹100', 1, NULL, NULL, '2025-03-01 16:45:00'),
(2004, 9001, 'bonus', 200, 'Welcome bonus - New user registration', NULL, 'bonus', NULL, '2025-01-15 10:30:00'),
(2005, 9001, 'earned', 75, 'Volunteer event participation', NULL, 'volunteer', NULL, '2025-02-15 09:00:00'),

-- User 9002 - Priya Patel
(2006, 9002, 'earned', 75, 'E-waste recycling completed', 1002, 'waste_report', '2026-03-12', '2025-03-12 16:00:00'),
(2007, 9002, 'earned', 150, 'Bulk pickup - Large items', NULL, 'waste_report', '2026-04-01', '2025-03-25 11:00:00'),
(2008, 9002, 'earned', 50, 'Quiz participation bonus', NULL, 'quiz', NULL, '2025-02-25 14:20:00'),
(2009, 9002, 'redeemed', -300, 'Flipkart 10% Discount Coupon', 2, NULL, NULL, '2025-03-10 10:00:00'),
(2010, 9002, 'bonus', 100, 'Referral bonus - Friend joined', NULL, 'referral', NULL, '2025-02-22 09:30:00'),

-- User 9003 - Amit Kumar (Commercial)
(2011, 9003, 'earned', 500, 'Monthly commercial waste pickup', NULL, 'waste_report', '2026-03-31', '2025-03-31 17:00:00'),
(2012, 9003, 'earned', 300, 'Large scale recycling drive', NULL, 'waste_report', '2026-03-15', '2025-03-15 12:00:00'),
(2013, 9003, 'earned', 200, 'Office waste segregation workshop', NULL, 'volunteer', NULL, '2025-02-10 14:00:00'),
(2014, 9003, 'redeemed', -1000, 'EcoConnect T-Shirt', 3, NULL, NULL, '2025-03-20 15:30:00'),
(2015, 9003, 'earned', 600, 'Quarterly sustainability report submission', NULL, 'bonus', NULL, '2025-01-31 11:00:00'),

-- User 9004 - Sunita Devi
(2016, 9004, 'earned', 40, 'Organic waste pickup completed', 1004, 'waste_report', '2026-03-18', '2025-03-18 12:00:00'),
(2017, 9004, 'earned', 30, 'Weekly quiz participation', NULL, 'quiz', NULL, '2025-03-05 09:00:00'),
(2018, 9004, 'earned', 60, 'Tree plantation event volunteer', NULL, 'volunteer', NULL, '2025-02-28 08:00:00'),
(2019, 9004, 'redeemed', -200, 'Free Waste Pickup Service', 4, NULL, NULL, '2025-03-25 10:00:00'),
(2020, 9004, 'bonus', 150, 'First report bonus', NULL, 'bonus', NULL, '2025-03-10 14:00:00'),

-- User 9005 - Vikram Singh
(2021, 9005, 'earned', 25, 'Waste spotting report approved', NULL, 'waste_report', NULL, '2025-03-28 11:30:00'),
(2022, 9005, 'earned', 45, 'Quiz - E-waste category', NULL, 'quiz', NULL, '2025-02-18 16:00:00'),
(2023, 9005, 'earned', 80, 'Clean-up drive participation', NULL, 'volunteer', NULL, '2025-03-15 07:00:00'),
(2024, 9005, 'redeemed', -150, 'Tree Planting Donation', 5, NULL, NULL, '2025-03-22 13:00:00'),
(2025, 9005, 'bonus', 100, 'Level upgrade bonus - Seedling to Sprout', NULL, 'bonus', NULL, '2025-03-01 10:00:00'),

-- User 9006 - Meera Gupta
(2026, 9006, 'bonus', 100, 'New user welcome bonus', NULL, 'bonus', NULL, '2025-03-15 13:30:00'),
(2027, 9006, 'earned', 50, 'Hazardous waste safe disposal', NULL, 'waste_report', '2026-04-10', '2025-03-30 11:00:00'),
(2028, 9006, 'earned', 30, 'Quiz participation', NULL, 'quiz', NULL, '2025-03-20 15:00:00'),
(2029, 9006, 'earned', 40, 'Recycling education workshop', NULL, 'volunteer', NULL, '2025-03-25 10:00:00'),

-- User 9007 - Arjun Reddy
(2030, 9007, 'earned', 200, 'Commercial bulk pickup', 1007, 'waste_report', '2026-03-20', '2025-03-20 18:00:00'),
(2031, 9007, 'earned', 300, 'Construction waste recycling', NULL, 'waste_report', '2026-03-25', '2025-03-25 16:30:00'),
(2032, 9007, 'earned', 150, 'Corporate sustainability event', NULL, 'volunteer', NULL, '2025-02-25 14:00:00'),
(2033, 9007, 'redeemed', -750, 'Sustainable Water Bottle', 6, NULL, NULL, '2025-03-28 11:00:00'),

-- User 9008 - Neha Verma
(2034, 9008, 'earned', 65, 'Illegal dumping report resolved', 1008, 'waste_report', NULL, '2025-03-26 12:00:00'),
(2035, 9008, 'earned', 100, 'Quiz champion - 10 quizzes completed', NULL, 'quiz', NULL, '2025-02-20 09:00:00'),
(2036, 9008, 'earned', 120, 'Community awareness campaign', NULL, 'volunteer', NULL, '2025-03-10 10:00:00'),
(2037, 9008, 'earned', 50, 'Referral bonus', NULL, 'referral', NULL, '2025-02-28 14:00:00'),
(2038, 9008, 'redeemed', -200, 'Free Pickup Service', 4, NULL, NULL, '2025-03-15 16:00:00'),

-- User 9010 - Ananya Roy (Eco Warrior)
(2039, 9010, 'earned', 150, 'Monthly waste pickup streak', NULL, 'waste_report', '2026-03-15', '2025-03-15 11:00:00'),
(2040, 9010, 'earned', 200, 'E-waste collection drive organized', NULL, 'waste_report', '2026-03-20', '2025-03-20 15:00:00'),
(2041, 9010, 'earned', 300, 'Volunteer team leader - 5 events', NULL, 'volunteer', NULL, '2025-02-15 08:00:00'),
(2042, 9010, 'earned', 250, 'Quiz master achievement', NULL, 'quiz', NULL, '2025-03-01 10:00:00'),
(2043, 9010, 'earned', 500, 'Environmental blog contribution', NULL, 'bonus', NULL, '2025-01-20 14:00:00'),
(2044, 9010, 'redeemed', -1000, 'EcoConnect T-Shirt', 3, NULL, NULL, '2025-02-10 12:00:00'),
(2045, 9010, 'redeemed', -500, 'Amazon Gift Card', 1, NULL, NULL, '2025-03-05 15:00:00'),
(2046, 9010, 'redeemed', -300, 'Flipkart Discount', 2, NULL, NULL, '2025-03-25 11:00:00'),
(2047, 9010, 'earned', 640, 'Referral chain bonus', NULL, 'referral', NULL, '2025-03-28 09:00:00')
ON DUPLICATE KEY UPDATE user_id = VALUES(user_id);
