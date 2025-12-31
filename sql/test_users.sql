/* Test Users for SkillShare Platform */
/* Execute this after running schema.sql */

USE skilshopDB;

-- Clear existing data (optional - use with caution)
-- SET FOREIGN_KEY_CHECKS = 0;
-- TRUNCATE TABLE admin_actions;
-- TRUNCATE TABLE ratings;
-- TRUNCATE TABLE bookings;
-- TRUNCATE TABLE skill_sessions;
-- TRUNCATE TABLE impact_factors;
-- TRUNCATE TABLE Categories;
-- TRUNCATE TABLE Users;
-- SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- TEST USERS
-- ============================================
-- Insert test users with hashed passwords
-- All passwords follow the format: Role123 (e.g., Learner123, Instructor123, Admin123)

INSERT INTO Users (full_name, email, password_hash, role, city, is_suspended, suspended_reason) VALUES
-- Learner Test User
-- Email: learner@test.com
-- Password: Learner123
('Test Learner', 'learner@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Toronto', FALSE, NULL),

-- Instructor Test User  
-- Email: instructor@test.com
-- Password: Instructor123
('Test Instructor', 'instructor@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'Vancouver', FALSE, NULL),

-- Admin Test User
-- Email: admin@test.com  
-- Password: Admin123
('Test Admin', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Montreal', FALSE, NULL);

-- ============================================
-- CATEGORIES
-- ============================================
INSERT INTO Categories (name, description) VALUES
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
INSERT INTO impact_factors (category_id, co2_saved_per_participant_kg) VALUES
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
-- SUMMARY
-- ============================================
-- SELECT 'Test users and categories created successfully!' AS status;
-- SELECT '===========================================' AS separator;
-- SELECT 'LOGIN CREDENTIALS:' AS info;
-- SELECT '===========================================' AS separator;
-- SELECT 'LEARNER:' AS role;
-- SELECT '  Email: learner@test.com' AS email;
-- SELECT '  Password: Learner123' AS password;
-- SELECT '' AS blank1;
-- SELECT 'INSTRUCTOR:' AS role2;
-- SELECT '  Email: instructor@test.com' AS email2;
-- SELECT '  Password: Instructor123' AS password2;
-- SELECT '' AS blank2;
-- SELECT 'ADMIN:' AS role3;
-- SELECT '  Email: admin@test.com' AS email3;
-- SELECT '  Password: Admin123' AS password3;
-- SELECT '===========================================' AS separator2;
-- SELECT CONCAT(COUNT(*), ' users created') AS users FROM Users;
-- SELECT CONCAT(COUNT(*), ' categories created') AS categories FROM Categories;
-- SELECT CONCAT(COUNT(*), ' impact factors set') AS impact_factors FROM impact_factors;
-- SELECT '===========================================' AS separator3;
