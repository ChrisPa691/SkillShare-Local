/* Dummy Data for SkillShare Platform */
/* Execute this after running schema.sql and test_users.sql */

USE skilshopDB;

-- ============================================
-- OPTIONAL: Clean existing dummy data before inserting
-- Uncomment the lines below if you want to reset all data
-- ============================================
-- SET FOREIGN_KEY_CHECKS = 0;
-- DELETE FROM admin_actions WHERE admin_id > 3;
-- DELETE FROM ratings WHERE session_id > 0;
-- DELETE FROM bookings WHERE booking_id > 0;
-- DELETE FROM skill_sessions WHERE session_id > 0;
-- DELETE FROM Users WHERE user_id > 3;
-- SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- USERS
-- ============================================
-- Additional instructors (beyond test_instructor)
INSERT IGNORE INTO Users (full_name, email, password_hash, role, city, is_suspended, suspended_reason) VALUES
('Sarah Martinez', 'sarah.martinez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'Toronto', FALSE, NULL),
('Michael Chen', 'michael.chen@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'Vancouver', FALSE, NULL),
('Emily Johnson', 'emily.johnson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'Montreal', FALSE, NULL),
('David Kim', 'david.kim@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'Calgary', FALSE, NULL),
('Jessica Brown', 'jessica.brown@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'Toronto', FALSE, NULL);

-- Additional learners (beyond test_learner)
INSERT IGNORE INTO Users (full_name, email, password_hash, role, city, is_suspended, suspended_reason) VALUES
('Alex Thompson', 'alex.thompson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Toronto', FALSE, NULL),
('Rachel Lee', 'rachel.lee@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Vancouver', FALSE, NULL),
('James Wilson', 'james.wilson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Montreal', FALSE, NULL),
('Maria Garcia', 'maria.garcia@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Calgary', FALSE, NULL),
('Tom Anderson', 'tom.anderson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Toronto', FALSE, NULL),
('Lisa Wang', 'lisa.wang@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Vancouver', FALSE, NULL),
('Robert Miller', 'robert.miller@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Montreal', FALSE, NULL),
('Sophie Turner', 'sophie.turner@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Toronto', FALSE, NULL),
('Kevin Patel', 'kevin.patel@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Calgary', FALSE, NULL),
('Nina Rodriguez', 'nina.rodriguez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Vancouver', FALSE, NULL);

-- One suspended user example
INSERT IGNORE INTO Users (full_name, email, password_hash, role, city, is_suspended, suspended_reason) VALUES
('John Suspended', 'suspended@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Toronto', TRUE, 'Violated community guidelines');

-- ============================================
-- CATEGORIES (if not already inserted by test_users.sql)
-- ============================================
INSERT IGNORE INTO Categories (name, description) VALUES
('Web Development', 'Learn modern web development skills including HTML, CSS, JavaScript, and frameworks'),
('Data Science', 'Master data analysis, machine learning, and statistical methods'),
('Design', 'UI/UX design, graphic design, and creative skills'),
('Business', 'Entrepreneurship, marketing, and business management skills'),
('Photography', 'Digital photography, editing, and visual storytelling'),
('Cooking', 'Culinary arts, baking, and food preparation techniques'),
('Fitness', 'Personal training, yoga, and wellness coaching'),
('Music', 'Instrument lessons, music theory, and production'),
('Languages', 'Learn new languages and improve communication skills'),
('Crafts', 'DIY projects, woodworking, and handmade crafts');

-- ============================================
-- IMPACT FACTORS
-- ============================================
INSERT IGNORE INTO impact_factors (skill_category, co2_saved_per_participant_kg) VALUES
(1, 2.5),  -- Web Development
(2, 3.0),  -- Data Science
(3, 1.8),  -- Design
(4, 2.0),  -- Business
(5, 1.5),  -- Photography
(6, 2.2),  -- Cooking
(7, 1.9),  -- Fitness
(8, 1.7),  -- Music
(9, 2.1),  -- Languages
(10, 1.6); -- Crafts

-- ============================================
-- SKILL SESSIONS
-- ============================================
-- Check and insert upcoming sessions only if they don't exist
-- Note: Sessions are identified by unique combination of instructor_id, title, and event_datetime
INSERT INTO skill_sessions (instructor_id, category_id, title, description, duration_minutes, fee_type, fee_amount, location_type, city, address, online_link, photo_url, event_datetime, total_capacity, capacity_remaining, status, sustainability_description)
SELECT * FROM (SELECT 2 as instructor_id, 1 as category_id, 'React.js Fundamentals' as title, 'Learn the basics of React and build your first component-based application' as description, 120 as duration_minutes, 'free' as fee_type, 0.00 as fee_amount, 'online' as location_type, NULL as city, NULL as address, 'https://meet.google.com/abc-defg-hij' as online_link, NULL as photo_url, '2025-01-15 18:00:00' as event_datetime, 25 as total_capacity, 15 as capacity_remaining, 'upcoming' as status, 'Online learning reduces travel emissions and promotes sustainable education' as sustainability_description) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM skill_sessions WHERE instructor_id = 2 AND title = 'React.js Fundamentals' AND event_datetime = '2025-01-15 18:00:00'
) LIMIT 1;

-- Continue with remaining sessions using simple INSERT IGNORE for brevity
INSERT IGNORE INTO skill_sessions (instructor_id, category_id, title, description, duration_minutes, fee_type, fee_amount, location_type, city, address, online_link, photo_url, event_datetime, total_capacity, capacity_remaining, status, sustainability_description) VALUES
-- Web Development Sessions
(2, 1, 'React.js Fundamentals', 'Learn the basics of React and build your first component-based application', 120, 'free', 0.00, 'online', NULL, NULL, 'https://meet.google.com/abc-defg-hij', NULL, '2025-01-15 18:00:00', 25, 15, 'upcoming', 'Online learning reduces travel emissions and promotes sustainable education'),
(4, 1, 'Full-Stack JavaScript Workshop', 'Build a complete web application using Node.js, Express, and MongoDB', 180, 'paid', 45.00, 'in-person', 'Toronto', '123 Tech Street, Toronto', NULL, NULL, '2025-01-20 14:00:00', 15, 10, 'upcoming', 'Local in-person sessions reduce long-distance travel impact'),
(5, 1, 'CSS Grid & Flexbox Mastery', 'Master modern CSS layout techniques for responsive design', 90, 'free', 0.00, 'online', NULL, NULL, 'https://zoom.us/j/123456789', NULL, '2025-01-18 19:00:00', 30, 25, 'upcoming', 'Virtual workshops eliminate commute emissions'),

-- Data Science Sessions
(4, 2, 'Introduction to Python for Data Science', 'Get started with Python programming for data analysis', 150, 'paid', 35.00, 'in-person', 'Vancouver', '456 Data Ave, Vancouver', NULL, NULL, '2025-01-22 13:00:00', 20, 12, 'upcoming', 'Community-based learning reduces carbon footprint'),
(5, 2, 'Machine Learning Basics', 'Understand fundamental ML algorithms and their applications', 120, 'free', 0.00, 'online', NULL, NULL, 'https://teams.microsoft.com/xyz', NULL, '2025-01-25 17:00:00', 40, 35, 'upcoming', 'Digital learning platform saves energy and resources'),

-- Design Sessions
(6, 3, 'UI/UX Design Principles', 'Learn user-centered design and create beautiful interfaces', 120, 'paid', 40.00, 'in-person', 'Montreal', '789 Creative Blvd, Montreal', NULL, NULL, '2025-01-17 15:00:00', 12, 8, 'upcoming', 'Local workshops minimize transportation emissions'),
(7, 3, 'Figma for Beginners', 'Master the basics of Figma for UI design and prototyping', 90, 'free', 0.00, 'online', NULL, NULL, 'https://meet.google.com/design-123', NULL, '2025-01-19 18:30:00', 25, 20, 'upcoming', 'Remote learning reduces environmental impact'),

-- Business Sessions
(8, 4, 'Digital Marketing Fundamentals', 'Learn effective online marketing strategies for small businesses', 120, 'paid', 50.00, 'in-person', 'Calgary', '321 Business Park, Calgary', NULL, NULL, '2025-01-23 16:00:00', 18, 15, 'upcoming', 'Local skill-sharing builds sustainable community networks'),
(9, 4, 'Start Your Own Business', 'Essential steps to launch and grow a successful startup', 180, 'free', 0.00, 'online', NULL, NULL, 'https://zoom.us/j/business101', NULL, '2025-01-21 14:00:00', 50, 45, 'upcoming', 'Online education platform reduces travel and facility energy use'),

-- Photography Sessions
(10, 5, 'Portrait Photography Basics', 'Learn techniques for capturing stunning portraits', 120, 'paid', 55.00, 'in-person', 'Toronto', '555 Photo Studio, Toronto', NULL, NULL, '2025-01-24 11:00:00', 10, 6, 'upcoming', 'Small local classes reduce per-capita environmental impact');

-- Completed sessions (for ratings and impact calculation)
INSERT IGNORE INTO skill_sessions (instructor_id, category_id, title, description, duration_minutes, fee_type, fee_amount, location_type, city, address, online_link, photo_url, event_datetime, total_capacity, capacity_remaining, status, sustainability_description) VALUES
(2, 1, 'HTML & CSS Crash Course', 'Quick introduction to web development fundamentals', 90, 'free', 0.00, 'online', NULL, NULL, 'https://meet.google.com/html-101', NULL, '2024-12-15 18:00:00', 30, 0, 'completed', 'Online learning eliminates commute emissions'),
(4, 2, 'Data Visualization with Python', 'Create compelling data visualizations using Matplotlib and Seaborn', 120, 'paid', 40.00, 'in-person', 'Toronto', '123 Data Street, Toronto', NULL, NULL, '2024-12-18 14:00:00', 15, 0, 'completed', 'Local workshops reduce travel carbon footprint'),
(6, 3, 'Graphic Design Essentials', 'Master the principles of effective graphic design', 150, 'free', 0.00, 'online', NULL, NULL, 'https://zoom.us/j/design-basics', NULL, '2024-12-20 16:00:00', 25, 0, 'completed', 'Virtual sessions save energy and resources'),
(8, 4, 'Social Media Marketing', 'Build an effective social media strategy for your business', 120, 'paid', 45.00, 'in-person', 'Vancouver', '789 Marketing Ave, Vancouver', NULL, NULL, '2024-12-22 13:00:00', 20, 0, 'completed', 'Community learning reduces environmental impact'),
(10, 5, 'Photography Composition', 'Learn the art of composing beautiful photographs', 90, 'free', 0.00, 'online', NULL, NULL, 'https://meet.google.com/photo-comp', NULL, '2024-12-10 19:00:00', 35, 0, 'completed', 'Online education platform minimizes carbon emissions'),

-- Cooking Sessions
(7, 6, 'Italian Cooking Masterclass', 'Learn to cook authentic Italian dishes', 150, 'paid', 60.00, 'in-person', 'Toronto', '100 Culinary St, Toronto', NULL, NULL, '2025-01-26 17:00:00', 12, 8, 'upcoming', 'Local food education promotes sustainable eating habits'),
(9, 6, 'Vegan Baking Workshop', 'Delicious plant-based baking techniques', 120, 'free', 0.00, 'online', NULL, NULL, 'https://zoom.us/j/vegan-bake', NULL, '2025-01-28 15:00:00', 40, 38, 'upcoming', 'Virtual cooking classes reduce travel and promote sustainability'),

-- Fitness Sessions
(5, 7, 'Yoga for Beginners', 'Introduction to yoga poses and breathing techniques', 60, 'free', 0.00, 'in-person', 'Montreal', '200 Wellness Center, Montreal', NULL, NULL, '2025-01-27 09:00:00', 15, 10, 'upcoming', 'Local fitness classes build healthy, sustainable communities'),

-- Canceled session example
(2, 1, 'Advanced JavaScript Patterns', 'Deep dive into advanced JS concepts', 120, 'paid', 50.00, 'online', NULL, NULL, 'https://meet.google.com/js-advanced', NULL, '2025-01-16 18:00:00', 20, 20, 'canceled', 'Session canceled due to instructor availability');

-- ============================================
-- BOOKINGS
-- ============================================
-- Bookings for completed sessions (for ratings)
-- Note: Using INSERT IGNORE to prevent duplicate bookings on reruns
INSERT IGNORE INTO bookings (session_id, learner_id, num_seats, status, requested_at, responded_at) VALUES
-- HTML & CSS Crash Course (session_id: 11) - completed
(11, 1, 1, 'accepted', '2024-12-10 10:00:00', '2024-12-10 11:00:00'),
(11, 7, 1, 'accepted', '2024-12-11 14:00:00', '2024-12-11 15:00:00'),
(11, 9, 1, 'accepted', '2024-12-12 09:00:00', '2024-12-12 10:00:00'),
(11, 11, 1, 'accepted', '2024-12-13 16:00:00', '2024-12-13 17:00:00'),

-- Data Visualization with Python (session_id: 12) - completed
(12, 1, 1, 'accepted', '2024-12-14 08:00:00', '2024-12-14 09:00:00'),
(12, 8, 1, 'accepted', '2024-12-14 10:00:00', '2024-12-14 11:00:00'),
(12, 10, 1, 'accepted', '2024-12-15 12:00:00', '2024-12-15 13:00:00'),

-- Graphic Design Essentials (session_id: 13) - completed
(13, 7, 1, 'accepted', '2024-12-16 14:00:00', '2024-12-16 15:00:00'),
(13, 9, 1, 'accepted', '2024-12-17 11:00:00', '2024-12-17 12:00:00'),
(13, 12, 1, 'accepted', '2024-12-18 09:00:00', '2024-12-18 10:00:00'),
(13, 13, 1, 'accepted', '2024-12-18 15:00:00', '2024-12-18 16:00:00'),

-- Social Media Marketing (session_id: 14) - completed
(14, 8, 1, 'accepted', '2024-12-19 10:00:00', '2024-12-19 11:00:00'),
(14, 11, 1, 'accepted', '2024-12-19 13:00:00', '2024-12-19 14:00:00'),
(14, 14, 1, 'accepted', '2024-12-20 08:00:00', '2024-12-20 09:00:00'),

-- Photography Composition (session_id: 15) - completed
(15, 1, 1, 'accepted', '2024-12-05 12:00:00', '2024-12-05 13:00:00'),
(15, 10, 1, 'accepted', '2024-12-06 10:00:00', '2024-12-06 11:00:00'),
(15, 13, 1, 'accepted', '2024-12-07 14:00:00', '2024-12-07 15:00:00');

-- Bookings for upcoming sessions
-- React.js Fundamentals (session_id: 1)
INSERT IGNORE INTO bookings (session_id, learner_id, num_seats, status, requested_at, responded_at) VALUES
(1, 7, 1, 'accepted', '2025-01-05 10:00:00', '2025-01-05 11:00:00'),
(1, 8, 1, 'accepted', '2025-01-06 14:00:00', '2025-01-06 15:00:00'),
(1, 9, 1, 'pending', '2025-01-08 09:00:00', NULL),
(1, 10, 1, 'pending', '2025-01-09 11:00:00', NULL);

-- Full-Stack JavaScript Workshop (session_id: 2)
INSERT IGNORE INTO bookings (session_id, learner_id, num_seats, status, requested_at, responded_at) VALUES
(2, 1, 1, 'accepted', '2025-01-07 12:00:00', '2025-01-07 13:00:00'),
(2, 11, 1, 'accepted', '2025-01-08 15:00:00', '2025-01-08 16:00:00'),
(2, 12, 1, 'pending', '2025-01-10 10:00:00', NULL);

-- CSS Grid & Flexbox Mastery (session_id: 3)
INSERT IGNORE INTO bookings (session_id, learner_id, num_seats, status, requested_at, responded_at) VALUES
(3, 13, 1, 'accepted', '2025-01-09 08:00:00', '2025-01-09 09:00:00'),
(3, 14, 1, 'pending', '2025-01-11 13:00:00', NULL);

-- Introduction to Python for Data Science (session_id: 4)
INSERT IGNORE INTO bookings (session_id, learner_id, num_seats, status, requested_at, responded_at) VALUES
(4, 7, 1, 'accepted', '2025-01-10 11:00:00', '2025-01-10 12:00:00'),
(4, 8, 1, 'accepted', '2025-01-11 14:00:00', '2025-01-11 15:00:00'),
(4, 1, 1, 'declined', '2025-01-12 09:00:00', '2025-01-12 10:00:00');

-- Machine Learning Basics (session_id: 5)
INSERT IGNORE INTO bookings (session_id, learner_id, num_seats, status, requested_at, responded_at) VALUES
(5, 9, 1, 'accepted', '2025-01-12 15:00:00', '2025-01-12 16:00:00'),
(5, 10, 1, 'pending', '2025-01-13 10:00:00', NULL);

-- UI/UX Design Principles (session_id: 6)
INSERT IGNORE INTO bookings (session_id, learner_id, num_seats, status, requested_at, responded_at) VALUES
(6, 11, 1, 'accepted', '2025-01-08 13:00:00', '2025-01-08 14:00:00'),
(6, 12, 1, 'accepted', '2025-01-09 16:00:00', '2025-01-09 17:00:00'),
(6, 1, 1, 'canceled', '2025-01-07 10:00:00', '2025-01-07 11:00:00');

-- Portrait Photography Basics (session_id: 10)
INSERT IGNORE INTO bookings (session_id, learner_id, num_seats, status, requested_at, responded_at) VALUES
(10, 13, 1, 'accepted', '2025-01-14 12:00:00', '2025-01-14 13:00:00'),
(10, 14, 1, 'accepted', '2025-01-15 09:00:00', '2025-01-15 10:00:00');

-- ============================================
-- RATINGS
-- ============================================
-- Ratings for completed sessions only
-- Note: Using INSERT IGNORE because of UNIQUE constraint on (session_id, learner_id)
INSERT IGNORE INTO ratings (session_id, learner_id, rating, comment, created_at) VALUES
-- HTML & CSS Crash Course (session_id: 11)
(11, 1, 5, 'Excellent introduction to web development! Very clear explanations.', '2024-12-16 10:00:00'),
(11, 7, 4, 'Great session, learned a lot. Would love more advanced topics.', '2024-12-16 14:00:00'),
(11, 9, 5, 'Perfect for beginners. Instructor was very patient and helpful.', '2024-12-17 09:00:00'),

-- Data Visualization with Python (session_id: 12)
(12, 1, 5, 'Outstanding workshop! Practical examples were very useful.', '2024-12-19 10:00:00'),
(12, 8, 4, 'Good content, but could use more time for hands-on practice.', '2024-12-19 15:00:00'),
(12, 10, 5, 'Loved it! Now I can create beautiful charts for my projects.', '2024-12-20 11:00:00'),

-- Graphic Design Essentials (session_id: 13)
(13, 7, 5, 'Amazing instructor! Learned so much about design principles.', '2024-12-21 13:00:00'),
(13, 9, 4, 'Very informative session. Would recommend to anyone interested in design.', '2024-12-21 16:00:00'),
(13, 12, 5, 'Best design workshop I have attended. Highly practical!', '2024-12-22 10:00:00'),

-- Social Media Marketing (session_id: 14)
(14, 8, 4, 'Useful strategies for growing social media presence.', '2024-12-23 12:00:00'),
(14, 11, 5, 'Excellent! Already implementing what I learned for my business.', '2024-12-23 15:00:00'),
(14, 14, 4, 'Good session with actionable tips.', '2024-12-24 09:00:00'),

-- Photography Composition (session_id: 15)
(15, 1, 5, 'My photos have improved dramatically! Thank you!', '2024-12-11 14:00:00'),
(15, 10, 5, 'Fantastic workshop. Learned the rule of thirds and much more.', '2024-12-12 10:00:00'),
(15, 13, 4, 'Great techniques for better composition. Highly recommended.', '2024-12-13 11:00:00');

-- ============================================
-- ADMIN ACTIONS
-- ============================================
-- Admin actions log (assuming admin user_id is 3)
-- Note: Using INSERT IGNORE to prevent duplicate log entries
INSERT IGNORE INTO admin_actions (admin_id, action_type, target_user_id, description, created_at) VALUES
(3, 'user_suspended', 17, 'Suspended user for violating community guidelines', '2024-12-28 10:30:00'),
(3, 'session_review', NULL, 'Reviewed and approved new session submissions', '2024-12-27 14:00:00'),
(3, 'category_added', NULL, 'Added new category: Crafts', '2024-12-26 11:00:00'),
(3, 'user_verified', 4, 'Verified instructor credentials for Michael Chen', '2024-12-25 09:00:00'),
(3, 'impact_updated', NULL, 'Updated CO2 impact factors for all categories', '2024-12-24 16:00:00'),
(3, 'booking_resolved', 8, 'Resolved booking dispute for user', '2024-12-23 13:30:00'),
(3, 'session_canceled', NULL, 'Canceled session due to instructor unavailability', '2024-12-22 10:00:00');

-- ============================================
-- SUMMARY
-- ============================================
-- SELECT 'Dummy data inserted successfully!' AS Status;
-- SELECT '=======================================' AS Separator;
-- SELECT CONCAT(COUNT(*), ' users created') AS Users FROM Users;
-- SELECT CONCAT(COUNT(*), ' categories created') AS Categories FROM Categories;
-- SELECT CONCAT(COUNT(*), ' sessions created') AS Sessions FROM skill_sessions;
-- SELECT CONCAT(COUNT(*), ' bookings created') AS Bookings FROM bookings;
-- SELECT CONCAT(COUNT(*), ' ratings created') AS Ratings FROM ratings;
-- SELECT CONCAT(COUNT(*), ' impact factors set') AS Impact_Factors FROM impact_factors;
-- SELECT CONCAT(COUNT(*), ' admin actions logged') AS Admin_Actions FROM admin_actions;
-- SELECT '=======================================' AS Separator;
-- SELECT 'Database ready for testing!' AS Status;
