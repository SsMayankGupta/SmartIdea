-- First, insert a dummy user if not exists
INSERT INTO users (id, full_name, email, phone, password_hash, user_type, green_points) 
VALUES (9991, 'Demo User', 'demo@ecoconnect.com', '+91-99999-99999', 'demo_hash', 'resident', 500)
ON DUPLICATE KEY UPDATE full_name = 'Demo User';

-- Insert dummy data into request_for_services table
INSERT INTO request_for_services (
    request_id, user_id, city, center_id, center_name, request_type, status,
    contact_name, contact_email, contact_phone, address, pincode,
    preferred_date, preferred_time, special_instructions, points_awarded
) VALUES
(
    'REQ-2026-00101', 9991, 'Noida', 1, 'Noida Sector 62 Recycling Center', 
    'Household Waste Pickup', 'Pending',
    'Rahul Sharma', 'rahul.sharma@email.com', '+91-98765-43210',
    'Flat 302, Tower B, Supertech Ecovillage, Sector 137, Noida',
    '201305', '2026-04-05', 'Morning (8AM-12PM)', 
    'Please ring doorbell twice', 50
),
(
    'REQ-2026-00102', 9991, 'Gurugram', 2, 'Gurugram Electronic Waste Center',
    'Recycling Services', 'Approved',
    'Priya Patel', 'priya.patel@email.com', '+91-98765-12345',
    'House 42, DLF Phase 2, Golf Course Road, Gurugram',
    '122002', '2026-04-03', 'Afternoon (12PM-4PM)',
    'Have old TV and computer to recycle', 75
),
(
    'REQ-2026-00103', 9991, 'Delhi', 3, 'Delhi Municipal Recycling Hub',
    'Bulk Pickup', 'In Progress',
    'Amit Kumar', 'amit.kumar@email.com', '+91-99887-66554',
    '12/4, Connaught Place, Near Central Park, New Delhi',
    '110001', '2026-04-02', 'Evening (4PM-8PM)',
    'Moving out, need furniture and mattress pickup', 100
),
(
    'REQ-2026-00104', 9991, 'Faridabad', 4, 'Faridabad Green Recycling',
    'Household Waste Pickup', 'Completed',
    'Sunita Devi', 'sunita.devi@email.com', '+91-98712-34567',
    'House 78, Sector 15, Main Market, Faridabad',
    '121007', '2026-03-28', 'Morning (8AM-12PM)',
    'Regular pickup, separate bins kept ready', 50
),
(
    'REQ-2026-00105', 9991, 'Noida', 1, 'Noida Sector 62 Recycling Center',
    'Recycling Services', 'Pending',
    'Vikram Singh', 'vikram.singh@email.com', '+91-98999-88877',
    'Tower 15, Jaypee Kosmos, Sector 134, Noida',
    '201304', '2026-04-07', 'Anytime',
    'Have plastic bottles and newspapers stacked', 75
),
(
    'REQ-2026-00106', 9991, 'Delhi', 3, 'Delhi Municipal Recycling Hub',
    'Hazardous Waste Handling', 'Pending',
    'Meera Gupta', 'meera.gupta@email.com', '+91-98123-45678',
    'Flat 201, Block C, Rajouri Garden, New Delhi',
    '110027', '2026-04-10', 'Morning (8AM-12PM)',
    'Old paint cans and chemicals from renovation', 150
),
(
    'REQ-2026-00107', 9991, 'Gurugram', 2, 'Gurugram Electronic Waste Center',
    'Bulk Pickup', 'Cancelled',
    'Arjun Reddy', 'arjun.reddy@email.com', '+91-96543-21098',
    'Villa 15, Unitech Nirvana Country, Sector 50, Gurugram',
    '122018', '2026-03-25', 'Evening (4PM-8PM)',
    'Cancelled - will reschedule next week', 0
);
