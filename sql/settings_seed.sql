-- ============================================================
-- SkillShare Local - Settings System Seed Data
-- ============================================================
-- This file populates the app_settings and impact_factors tables
-- with initial default values
-- ============================================================

-- ============================================================
-- SEED DATA: app_settings
-- ============================================================

-- Clear existing settings (for clean re-seeding during development)
-- TRUNCATE TABLE app_settings;

-- -----------------------------------------------------------
-- GROUP: security (Security & Authentication)
-- -----------------------------------------------------------
INSERT INTO app_settings (setting_key, setting_value, value_type, group_name, description, is_public, is_editable) VALUES
('security.session_timeout_minutes', '30', 'int', 'security', 'Session timeout duration in minutes (idle time before auto-logout)', FALSE, TRUE),
('security.max_login_attempts', '5', 'int', 'security', 'Maximum failed login attempts before account lockout', FALSE, TRUE),
('security.remember_me_days', '30', 'int', 'security', 'Number of days "Remember Me" cookie remains valid', FALSE, TRUE),
('security.csrf_enabled', 'true', 'bool', 'security', 'Enable CSRF token protection on forms', FALSE, TRUE);

-- -----------------------------------------------------------
-- GROUP: user (User & Roles)
-- -----------------------------------------------------------
INSERT INTO app_settings (setting_key, setting_value, value_type, group_name, description, is_public, is_editable) VALUES
('user.default_role', 'student', 'string', 'user', 'Default role assigned to new users (student, teacher, admin)', FALSE, TRUE),
('user.allow_registration', 'true', 'bool', 'user', 'Allow public user registration (disable for invite-only mode)', TRUE, TRUE);

-- -----------------------------------------------------------
-- GROUP: booking (Sessions & Bookings)
-- -----------------------------------------------------------
INSERT INTO app_settings (setting_key, setting_value, value_type, group_name, description, is_public, is_editable) VALUES
('booking.default_capacity', '10', 'int', 'booking', 'Default maximum participants per session', FALSE, TRUE),
('booking.auto_approve', 'true', 'bool', 'booking', 'Auto-approve booking requests (false = manual approval required)', FALSE, TRUE),
('booking.allow_free_sessions', 'true', 'bool', 'booking', 'Allow teachers to create sessions without payment requirements', TRUE, TRUE),
('booking.currency', 'GBP', 'string', 'booking', 'Display currency for session pricing (GBP, USD, EUR, etc.)', TRUE, TRUE);

-- -----------------------------------------------------------
-- GROUP: rating (Ratings & Feedback)
-- -----------------------------------------------------------
INSERT INTO app_settings (setting_key, setting_value, value_type, group_name, description, is_public, is_editable) VALUES
('rating.scale_max', '5', 'int', 'rating', 'Maximum rating value (e.g., 5 for 5-star system)', TRUE, TRUE),
('rating.allow_comments', 'true', 'bool', 'rating', 'Allow users to leave text comments with ratings', TRUE, TRUE),
('rating.allow_anonymous', 'false', 'bool', 'rating', 'Allow anonymous ratings (hides rater name from teachers)', FALSE, TRUE);

-- -----------------------------------------------------------
-- GROUP: impact (Sustainability Impact)
-- -----------------------------------------------------------
INSERT INTO app_settings (setting_key, setting_value, value_type, group_name, description, is_public, is_editable) VALUES
('impact.enable_tracking', 'true', 'bool', 'impact', 'Enable sustainability impact tracking and dashboard', TRUE, TRUE),
('impact.display_unit', 'kg CO₂', 'string', 'impact', 'Display unit for sustainability metrics', TRUE, TRUE),
('impact.disclaimer_text', 'Estimates based on community learning reducing travel and resource consumption.', 'string', 'impact', 'Disclaimer text shown on Impact Dashboard', TRUE, TRUE);

-- -----------------------------------------------------------
-- GROUP: ui (UI / UX)
-- -----------------------------------------------------------
INSERT INTO app_settings (setting_key, setting_value, value_type, group_name, description, is_public, is_editable) VALUES
('ui.sessions_per_page', '12', 'int', 'ui', 'Number of sessions to display per page in listings', TRUE, TRUE),
('ui.dashboard_items_per_page', '5', 'int', 'ui', 'Number of items to show per page on dashboards', TRUE, TRUE),
('ui.show_impact_badges', 'true', 'bool', 'ui', 'Display sustainability badges on session cards', TRUE, TRUE),
('ui.maintenance_mode', 'false', 'bool', 'ui', 'Enable maintenance mode (disables public access)', FALSE, TRUE);


-- ============================================================
-- SEED DATA: impact_factors
-- ============================================================

-- Clear existing factors (for clean re-seeding during development)
-- TRUNCATE TABLE impact_factors;

-- -----------------------------------------------------------
-- Sustainability Impact Factors by Skill Category
-- -----------------------------------------------------------
-- CO2 values are estimates based on reduced travel, shared resources, 
-- and community-based learning vs. traditional methods
-- -----------------------------------------------------------

INSERT INTO impact_factors (skill_category, co2_saved_per_participant_kg, source_note, is_active) VALUES
('Cooking', 2.50, 'Estimated savings from shared kitchen use, bulk ingredient sourcing, and reduced food waste compared to individual cooking courses', TRUE),
('Programming', 1.80, 'Reduced travel to coding bootcamps and shared digital resources vs. individual online subscriptions', TRUE),
('Gardening', 3.20, 'Community garden tool sharing, reduced transport to garden centers, and local knowledge exchange', TRUE),
('Language Learning', 1.50, 'Local face-to-face learning reducing online server energy and eliminating commute to language schools', TRUE),
('Music', 2.00, 'Shared instrument use, reduced travel to music studios, and community practice spaces', TRUE),
('Art & Crafts', 2.80, 'Material sharing, reduced packaging waste, and local skill transfer vs. purchasing new supplies', TRUE),
('Fitness & Yoga', 3.50, 'Local outdoor sessions, shared equipment, eliminating gym commutes and facility energy use', TRUE),
('Photography', 1.90, 'Shared camera equipment, local on-location learning, and reduced travel to photography courses', TRUE),
('DIY & Home Repair', 4.00, 'Tool sharing, preventing premature replacement of goods, and local repair skills transfer', TRUE),
('Business & Finance', 1.20, 'Digital resource sharing and local mentorship vs. travel to business seminars', TRUE),
('Technology & IT', 1.60, 'Hardware sharing for workshops, reduced e-waste through repair skills, and local training', TRUE),
('Writing & Literature', 0.80, 'Book sharing, local writing groups, and reduced printing through digital collaboration', TRUE),
('Dance', 2.20, 'Community space use, shared music resources, and local practice vs. studio commutes', TRUE),
('Science & Education', 1.70, 'Shared laboratory equipment, local STEM mentorship, and reduced travel to tutoring centers', TRUE),
('Sustainability & Environment', 5.00, 'Direct environmental action, community composting, upcycling workshops, and zero-waste skills', TRUE);

-- -----------------------------------------------------------
-- Notes on Impact Calculation Methodology:
-- -----------------------------------------------------------
-- These estimates assume:
-- - Average 5km reduced travel per participant (vs. commercial alternatives)
-- - Shared resource use (2-3x efficiency vs. individual consumption)
-- - Knowledge retention leading to long-term behavioral change
-- - Community-based learning reducing infrastructure energy use
--
-- CO2 calculations are conservative estimates. Actual impact varies by:
-- - Session duration and frequency
-- - Number of participants
-- - Local transportation methods
-- - Resource sharing practices
-- -----------------------------------------------------------
