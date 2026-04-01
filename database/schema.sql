-- EcoConnect Database Schema
-- Comprehensive database structure for waste management platform

-- Create database
CREATE DATABASE IF NOT EXISTS ecoconnect 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE ecoconnect;

-- ============================================
-- USERS TABLE
-- Stores user account information
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    city VARCHAR(50),
    area VARCHAR(100),
    user_type ENUM('resident', 'commercial', 'admin') DEFAULT 'resident',
    green_points INT DEFAULT 0,
    level ENUM('Seedling', 'Sprout', 'Sapling', 'Tree', 'Eco Warrior') DEFAULT 'Seedling',
    profile_image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
) ENGINE=InnoDB;

-- ============================================
-- WASTE REPORTS TABLE
-- Stores waste pickup requests and reports
-- ============================================
CREATE TABLE IF NOT EXISTS waste_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    report_type ENUM('pickup_request', 'waste_spotting', 'illegal_dumping') DEFAULT 'pickup_request',
    waste_type ENUM('plastic', 'organic', 'e_waste', 'general', 'hazardous', 'construction') NOT NULL,
    waste_category VARCHAR(50),
    location_address VARCHAR(255) NOT NULL,
    city VARCHAR(50) NOT NULL,
    pincode VARCHAR(10),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    description TEXT,
    image_path VARCHAR(255),
    status ENUM('pending', 'assigned', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    scheduled_date DATE,
    scheduled_time TIME,
    assigned_driver_id INT,
    completion_notes TEXT,
    points_earned INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_user_id (user_id),
    INDEX idx_location (city, pincode)
) ENGINE=InnoDB;

-- ============================================
-- GREEN POINTS TRANSACTIONS
-- Tracks all point earnings and redemptions
-- ============================================
CREATE TABLE IF NOT EXISTS green_points_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    transaction_type ENUM('earned', 'redeemed', 'bonus', 'expired') NOT NULL,
    points INT NOT NULL,
    description VARCHAR(255),
    reference_id INT,
    reference_type ENUM('waste_report', 'quiz', 'volunteer', 'referral', 'bonus') NULL,
    expiry_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_transaction_type (transaction_type)
) ENGINE=InnoDB;

-- ============================================
-- RECYCLING CENTERS TABLE
-- Stores recycling facility information
-- ============================================
CREATE TABLE IF NOT EXISTS recycling_centers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('municipal', 'private', 'ngo', 'scrap_dealer') NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50),
    pincode VARCHAR(10),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    phone VARCHAR(20),
    email VARCHAR(100),
    operating_hours VARCHAR(100),
    accepted_waste_types JSON,
    services_offered JSON,
    is_active BOOLEAN DEFAULT TRUE,
    rating DECIMAL(2, 1) DEFAULT 0.0,
    total_reviews INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_city (city),
    INDEX idx_location (latitude, longitude),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- ============================================
-- SERVICES TABLE
-- Available waste management services
-- ============================================
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    category ENUM('residential', 'commercial', 'industrial', 'institutional') NOT NULL,
    icon VARCHAR(50),
    price_type ENUM('free', 'paid', 'subscription') DEFAULT 'free',
    base_price DECIMAL(10, 2) DEFAULT 0.00,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_category (category),
    INDEX idx_active (is_active),
    INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- ============================================
-- DUSTBIN BOOKINGS TABLE
-- Office/School dustbin requests
-- ============================================
CREATE TABLE IF NOT EXISTS dustbin_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    organization_name VARCHAR(100) NOT NULL,
    organization_type ENUM('office', 'school', 'college', 'apartment', 'hospital', 'mall') NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address VARCHAR(255),
    city VARCHAR(50),
    pincode VARCHAR(10),
    dustbin_type ENUM('plastic', 'paper', 'e_waste', 'general', 'organic', 'mixed') NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    preferred_delivery_date DATE,
    special_instructions TEXT,
    status ENUM('pending', 'confirmed', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_city (city)
) ENGINE=InnoDB;

-- ============================================
-- VOLUNTEER APPLICATIONS TABLE
-- Volunteer registration and management
-- ============================================
CREATE TABLE IF NOT EXISTS volunteers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    city VARCHAR(50) NOT NULL,
    area_of_interest ENUM('tree_plantation', 'clean_up', 'recycling_awareness', 'community_programs', 'waste_segregation') NOT NULL,
    availability VARCHAR(100),
    experience TEXT,
    motivation TEXT,
    status ENUM('pending', 'approved', 'rejected', 'inactive') DEFAULT 'pending',
    total_hours INT DEFAULT 0,
    events_participated INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_city (city)
) ENGINE=InnoDB;

-- ============================================
-- QUIZ QUESTIONS TABLE
-- Recycling quiz content
-- ============================================
CREATE TABLE IF NOT EXISTS quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    question_type ENUM('multiple_choice', 'true_false', 'image_based') DEFAULT 'multiple_choice',
    options JSON NOT NULL,
    correct_answer INT NOT NULL,
    explanation TEXT,
    category ENUM('general', 'plastic', 'e_waste', 'organic', 'paper', 'metal', 'glass') DEFAULT 'general',
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'easy',
    points INT DEFAULT 10,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_category (category),
    INDEX idx_difficulty (difficulty),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- ============================================
-- QUIZ ATTEMPTS TABLE
-- Track user quiz participation
-- ============================================
CREATE TABLE IF NOT EXISTS quiz_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    correct_answers INT NOT NULL,
    points_earned INT DEFAULT 0,
    time_taken INT,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_score (score)
) ENGINE=InnoDB;

-- ============================================
-- REWARDS/REDEEMPTIONS TABLE
-- Available rewards and user redemptions
-- ============================================
CREATE TABLE IF NOT EXISTS rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category ENUM('gift_card', 'discount', 'merchandise', 'service', 'donation') NOT NULL,
    points_required INT NOT NULL,
    quantity_available INT DEFAULT -1,
    quantity_redeemed INT DEFAULT 0,
    image_path VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_category (category),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- ============================================
-- USER REWARD REDEMPTIONS
-- Track redeemed rewards
-- ============================================
CREATE TABLE IF NOT EXISTS user_rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reward_id INT NOT NULL,
    points_spent INT NOT NULL,
    status ENUM('pending', 'processing', 'delivered', 'cancelled') DEFAULT 'pending',
    delivery_address VARCHAR(255),
    redeemed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivered_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reward_id) REFERENCES rewards(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============================================
-- SUSTAINABILITY EVENTS TABLE
-- Clean city initiatives and events
-- ============================================
CREATE TABLE IF NOT EXISTS sustainability_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    event_type ENUM('clean_up', 'tree_plantation', 'awareness_campaign', 'workshop', 'collection_drive') NOT NULL,
    location VARCHAR(255),
    city VARCHAR(50),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    event_date DATE NOT NULL,
    event_time TIME,
    duration_hours INT,
    max_participants INT DEFAULT 0,
    registered_count INT DEFAULT 0,
    points_reward INT DEFAULT 0,
    organizer_name VARCHAR(100),
    organizer_contact VARCHAR(20),
    image_path VARCHAR(255),
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_event_type (event_type),
    INDEX idx_city (city),
    INDEX idx_status (status),
    INDEX idx_event_date (event_date)
) ENGINE=InnoDB;

-- ============================================
-- EVENT REGISTRATIONS TABLE
-- User event signups
-- ============================================
CREATE TABLE IF NOT EXISTS event_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    attended BOOLEAN DEFAULT FALSE,
    points_awarded INT DEFAULT 0,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES sustainability_events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (event_id, user_id),
    INDEX idx_event_id (event_id)
) ENGINE=InnoDB;

-- ============================================
-- IMPACT STATISTICS TABLE
-- Platform-wide environmental impact tracking
-- ============================================
CREATE TABLE IF NOT EXISTS impact_statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    metric_name VARCHAR(50) NOT NULL UNIQUE,
    metric_value DECIMAL(15, 2) DEFAULT 0,
    unit VARCHAR(20),
    description TEXT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- ANNOUNCEMENTS/NOTIFICATIONS TABLE
-- System announcements for users
-- ============================================
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('general', 'urgent', 'feature', 'event', 'reward') DEFAULT 'general',
    target_audience ENUM('all', 'residents', 'commercial', 'volunteers') DEFAULT 'all',
    is_active BOOLEAN DEFAULT TRUE,
    publish_date DATE,
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_type (type),
    INDEX idx_active (is_active),
    INDEX idx_target (target_audience)
) ENGINE=InnoDB;

-- ============================================
-- CONTACT/FEEDBACK TABLE
-- User inquiries and feedback
-- ============================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(100),
    message TEXT NOT NULL,
    category ENUM('general', 'complaint', 'suggestion', 'partnership', 'support') DEFAULT 'general',
    status ENUM('new', 'in_progress', 'resolved', 'closed') DEFAULT 'new',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    
    INDEX idx_status (status),
    INDEX idx_category (category)
) ENGINE=InnoDB;

-- ============================================
-- SERVICE REQUESTS TABLE (request_for_services)
-- Complete waste management service bookings
-- ============================================
CREATE TABLE IF NOT EXISTS request_for_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    city VARCHAR(50) NOT NULL,
    center_id INT NOT NULL,
    center_name VARCHAR(100),
    request_type ENUM('Household Waste Pickup', 'Industrial Waste Disposal', 'Recycling Services', 'Hazardous Waste Handling', 'Bulk Pickup') NOT NULL,
    status ENUM('Pending', 'Approved', 'In Progress', 'Completed', 'Cancelled') DEFAULT 'Pending',
    contact_name VARCHAR(100),
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20),
    address TEXT,
    pincode VARCHAR(10),
    preferred_date DATE,
    preferred_time VARCHAR(20),
    special_instructions TEXT,
    points_awarded INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    assigned_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_city (city),
    INDEX idx_request_type (request_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;

-- ============================================
-- INSERT DEFAULT DATA
-- ============================================

-- Insert default services
INSERT INTO services (name, slug, description, category, icon, price_type, base_price, display_order) VALUES
('Waste Collection', 'waste-collection', 'Door-to-door scheduled and on-demand waste pickup services for households and businesses.', 'residential', '🚛', 'free', 0.00, 1),
('Recycling Services', 'recycling-services', 'Eco-friendly recycling for plastic, paper, metal, and glass materials.', 'residential', '♻️', 'free', 0.00, 2),
('Bulk Waste Pickup', 'bulk-waste', 'Safe disposal of large waste such as furniture and construction debris.', 'residential', '🗑️', 'paid', 150.00, 3),
('Smart Bin Monitoring', 'smart-bin', 'IoT-enabled bins to track fill levels and prevent overflow.', 'commercial', '📡', 'subscription', 500.00, 4),
('Complaints & Requests', 'complaints', 'Report issues, track service status, and get quick resolutions.', 'residential', '📝', 'free', 0.00, 5),
('Rewards Program', 'rewards', 'Earn green points for recycling and redeem exciting rewards.', 'residential', '🎁', 'free', 0.00, 6),
('Commercial Waste Management', 'commercial-waste', 'Scalable waste management solutions for offices and institutions.', 'commercial', '🏢', 'subscription', 1000.00, 7),
('Residential Recycling', 'residential-recycling', 'Comprehensive recycling solutions for households.', 'residential', '🏠', 'free', 0.00, 8);

-- Insert default impact statistics
INSERT INTO impact_statistics (metric_name, metric_value, unit, description) VALUES
('tons_recycled', 1200, 'tons', 'Total tons of waste recycled through the platform'),
('active_centers', 45, 'centers', 'Number of active recycling centers partnered'),
('registered_users', 10000, 'users', 'Total registered users on the platform'),
('landfill_reduction', 30, 'percent', 'Percentage reduction in landfill waste'),
('trees_saved', 5000, 'trees', 'Estimated trees saved through recycling efforts'),
('co2_reduced', 2500, 'tons', 'CO2 emissions reduced in tons'),
('clean_up_events', 150, 'events', 'Total clean-up drives organized'),
('volunteer_hours', 8000, 'hours', 'Total volunteer hours contributed');

-- Insert default rewards
INSERT INTO rewards (name, description, category, points_required, quantity_available, image_path) VALUES
('Amazon Gift Card ₹100', 'Redeem for a ₹100 Amazon gift card', 'gift_card', 500, 100, 'rewards/amazon_card.png'),
('Flipkart Discount 10%', 'Get 10% off on eco-friendly products', 'discount', 300, -1, 'rewards/flipkart_discount.png'),
('EcoConnect T-Shirt', 'Exclusive EcoConnect branded merchandise', 'merchandise', 1000, 50, 'rewards/tshirt.png'),
('Free Waste Pickup', 'One free doorstep waste collection', 'service', 200, -1, 'rewards/free_pickup.png'),
('Plant a Tree', 'We will plant a tree in your name', 'donation', 150, -1, 'rewards/tree_planting.png'),
('Sustainable Water Bottle', 'Reusable stainless steel water bottle', 'merchandise', 750, 30, 'rewards/water_bottle.png');

-- Insert sample quiz questions
INSERT INTO quiz_questions (question, options, correct_answer, explanation, category, difficulty, points) VALUES
('Which bin should you use for plastic bottles?', '["Wet waste bin", "Dry waste bin", "E-waste bin", "Hazardous bin"]', 1, 'Plastic bottles go in the dry waste bin for recycling.', 'plastic', 'easy', 10),
('Can batteries be thrown in regular trash?', '["Yes, always", "No, they contain harmful chemicals", "Only if they are dead", "Only small batteries"]', 1, 'Batteries contain harmful chemicals and must be disposed of at e-waste centers.', 'e_waste', 'easy', 15),
('How long does it take for a plastic bottle to decompose?', '["10 years", "50 years", "100 years", "450 years"]', 3, 'Plastic bottles take approximately 450 years to decompose in landfills.', 'plastic', 'medium', 20),
('Which of these is NOT recyclable?', '["Newspaper", "Pizza box with grease", "Glass bottle", "Aluminum can"]', 1, 'Pizza boxes contaminated with grease cannot be recycled due to food residue.', 'general', 'medium', 15),
('What is the first R in the 3Rs of waste management?', '["Reduce", "Reuse", "Recycle", "Repair"]', 0, 'The 3Rs are Reduce, Reuse, and Recycle - in that order of priority.', 'general', 'easy', 10);

-- Insert sample recycling centers (NCR region)
INSERT INTO recycling_centers (name, type, address, city, state, pincode, latitude, longitude, phone, operating_hours, accepted_waste_types, services_offered, rating) VALUES
('Noida Sector 62 Recycling Center', 'municipal', 'Near Metro Station, Sector 62', 'Noida', 'Uttar Pradesh', '201309', 28.6258, 77.3580, '+91-120-1234567', '9:00 AM - 6:00 PM', '["plastic", "paper", "metal", "glass"]', '["drop_off", "pickup", "sorting"]', 4.5),
('Gurugram Electronic Waste Center', 'private', 'DLF Phase 2, Golf Course Road', 'Gurugram', 'Haryana', '122002', 28.4803, 77.0845, '+91-124-9876543', '10:00 AM - 7:00 PM', '["e_waste", "batteries"]', '["e_waste_collection", "data_destruction", "certification"]', 4.8),
('Delhi Municipal Recycling Hub', 'municipal', 'Connaught Place, Near Central Park', 'Delhi', 'Delhi', '110001', 28.6304, 77.2177, '+91-11-23456789', '8:00 AM - 8:00 PM', '["plastic", "paper", "metal", "glass", "organic"]', '["drop_off", "composting", "awareness_programs"]', 4.2),
('Faridabad Green Recycling', 'ngo', 'Sector 15, Main Market', 'Faridabad', 'Haryana', '121007', 28.4089, 77.3178, '+91-129-4567890', '9:00 AM - 5:00 PM', '["plastic", "paper", "old_clothes"]', '["drop_off", "donation_drive", "community_education"]', 4.6);

-- Insert sample events
INSERT INTO sustainability_events (title, description, event_type, location, city, event_date, event_time, duration_hours, max_participants, points_reward, organizer_name, status) VALUES
('Clean-Up Drive - India Gate', 'Join us for a massive clean-up drive around India Gate area. All equipment provided.', 'clean_up', 'India Gate, New Delhi', 'Delhi', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '07:00:00', 3, 100, 50, 'EcoConnect Delhi Team', 'upcoming'),
('Tree Plantation - Sector 62 Park', 'Help us plant 100 trees in Noida Sector 62 park area. Lunch will be provided.', 'tree_plantation', 'Sector 62 Park, Noida', 'Noida', DATE_ADD(CURDATE(), INTERVAL 14 DAY), '09:00:00', 4, 50, 75, 'Green Earth NGO', 'upcoming'),
('Recycling Workshop', 'Learn effective waste segregation and recycling techniques for homes and offices.', 'workshop', 'Community Center, DLF Phase 1', 'Gurugram', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '14:00:00', 2, 30, 40, 'EcoConnect Education Team', 'upcoming'),
('E-Waste Collection Drive', 'Bring your old electronics for safe recycling. Get bonus green points!', 'collection_drive', 'City Center Mall, Sector 12', 'Noida', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '10:00:00', 6, 200, 100, 'Tech Recyclers India', 'upcoming');

-- Insert sample announcements
INSERT INTO announcements (title, content, type, target_audience, publish_date, is_active) VALUES
('Welcome to EcoConnect!', 'Join our community and start your journey towards a sustainable future. Earn green points for every eco-friendly action!', 'general', 'all', CURDATE(), TRUE),
('New Rewards Added!', 'Check out our latest rewards including Amazon gift cards and eco-friendly merchandise. Start redeeming your green points today!', 'reward', 'all', CURDATE(), TRUE),
('Weekend Clean-Up Drive', 'Register for our weekend clean-up drive at India Gate and earn 50 bonus green points. Limited spots available!', 'event', 'all', CURDATE(), TRUE);
