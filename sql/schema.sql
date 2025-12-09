/* SQL Schema for Skill Development Workshop Platform */
/* This schema is designed to be used with MySQL */

CREATE DATABASE IF NOT EXISTS skilshopDB;
USE skilshopDB;

/* Users: Instructors, Learners, Admins */
CREATE TABLE IF NOT EXISTS Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('instructor', 'learner', 'admin') NOT NULL,
    city VARCHAR(50),
    is_suspended BOOLEAN DEFAULT FALSE,
    suspended_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_city (city)
);

/* Workshop Categories */
CREATE TABLE IF NOT EXISTS Categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
);

/* Skill Sessions store webinars, workshops, etc. */
CREATE TABLE IF NOT EXISTS skill_sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    instructor_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    duration_minutes INT NOT NULL,
    fee_type ENUM('free', 'paid') NOT NULL DEFAULT 'free',
    fee_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 CHECK (fee_amount >= 0),
    location_type ENUM('online', 'in-person') NOT NULL,
    city VARCHAR(50),
    address TEXT,
    online_link TEXT,
    photo_url TEXT,
    event_datetime TIMESTAMP NOT NULL,
    total_capacity INT NOT NULL CHECK (total_capacity > 0),
    capacity_remaining INT NOT NULL CHECK (capacity_remaining >= 0 AND capacity_remaining <= total_capacity),
    status ENUM('upcoming', 'completed', 'canceled') NOT NULL DEFAULT 'upcoming',
    sustainability_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Categories(category_id) ON DELETE RESTRICT,
    INDEX idx_instructor (instructor_id),
    INDEX idx_category (category_id),
    INDEX idx_event_datetime (event_datetime),
    INDEX idx_status (status),
    INDEX idx_city (city)
);

/* Bookings: Requests and Confirmations */
CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    learner_id INT NOT NULL,
    num_seats INT NOT NULL DEFAULT 1 CHECK (num_seats > 0),
    status ENUM('pending', 'accepted', 'declined', 'canceled') NOT NULL DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP NULL,
    FOREIGN KEY (session_id) REFERENCES skill_sessions(session_id) ON DELETE CASCADE,
    FOREIGN KEY (learner_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    INDEX idx_session (session_id),
    INDEX idx_learner (learner_id),
    INDEX idx_status (status),
    UNIQUE KEY unique_active_booking (session_id, learner_id, status)
);

/* Ratings for instructors from learners */
CREATE TABLE IF NOT EXISTS ratings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    learner_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES skill_sessions(session_id) ON DELETE CASCADE,
    FOREIGN KEY (learner_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_rating_per_session (session_id, learner_id),
    INDEX idx_session (session_id),
    INDEX idx_learner (learner_id)
);

/* Impact factors for sustainability per category */
CREATE TABLE IF NOT EXISTS impact_factors (
    impact_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNIQUE NOT NULL,
    co2_saved_per_participant_kg DECIMAL(10,2) NOT NULL DEFAULT 0.00 CHECK (co2_saved_per_participant_kg >= 0),
    FOREIGN KEY (category_id) REFERENCES Categories(category_id) ON DELETE CASCADE
);

/* Admin Actions Log */
CREATE TABLE IF NOT EXISTS admin_actions (
    action_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    target_user_id INT,
    description TEXT,
    action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (target_user_id) REFERENCES Users(user_id) ON DELETE SET NULL,
    INDEX idx_admin (admin_id),
    INDEX idx_target (target_user_id),
    INDEX idx_action_type (action_type)
);
