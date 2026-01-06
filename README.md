# SkillShare Local: Eco-Friendly Skill-Sharing Platform

## Academic Project Information

**Course Code**: ACSC476  
**Course Title**: Web Programming  
**Institution**: Frederick University  
**Academic Year**: 2025-2026  
**Semester**: Fall 2025  
**Student Name**: Christos Paparistodimou  
**Student ID**: ST024449  
**Submission Date**: January 2026  
**Project Version**: 1.0.0

---

## Abstract

This project presents SkillShare Local, a comprehensive web-based platform designed to facilitate the exchange of eco-friendly skills and sustainable practices within local communities. The system implements a multi-role architecture supporting three distinct user types: learners, instructors, and administrators. Learners can discover and book skill-sharing sessions, instructors can create and manage workshops, and administrators maintain platform integrity through comprehensive management tools.

The platform incorporates environmental impact tracking, calculating and visualizing estimated CO₂ savings and other sustainability metrics based on session participation. This feature promotes community awareness and encourages participation in environmentally conscious practices.

Developed as a comprehensive web application project, SkillShare Local demonstrates proficiency in full-stack web development, database design, user authentication, role-based access control, and modern web technologies.

---

## Project Overview

### Purpose and Scope

SkillShare Local addresses the growing need for community-based learning platforms focused on sustainability and environmental awareness. The platform serves as a bridge between skilled instructors offering eco-friendly workshops and learners seeking to develop sustainable practices.

### Key Objectives

1. **Facilitate Skill Exchange**: Enable instructors to share expertise in eco-friendly practices through structured workshops
2. **Promote Sustainability**: Track and visualize environmental impact of community learning initiatives
3. **Streamline Booking Process**: Provide an intuitive workflow for session discovery, booking, and confirmation
4. **Enable Quality Feedback**: Implement a comprehensive rating and review system
5. **Support Platform Management**: Offer administrative tools for user management and platform oversight

### Target Audience

- **Primary Users**: Community members interested in sustainable living and skill development
- **Instructors**: Individuals with expertise in eco-friendly practices and sustainable techniques
- **Administrators**: Platform managers responsible for content moderation and user management

---

## Technical Specifications

### Development Environment

- **Web Server**: Apache 2.4+ (XAMPP)
- **Server-Side Language**: PHP 8.0+
- **Database Management System**: MySQL 5.7+
- **Version Control**: Git
- **Development Tools**: Visual Studio Code, phpMyAdmin

### Technology Stack

#### Frontend Technologies
- **HTML5**: Semantic markup and structure
- **CSS3**: Styling and visual presentation
- **Bootstrap 5.3**: Responsive framework and UI components
- **JavaScript (ES6+)**: Client-side interactivity and validation
- **jQuery 3.6**: DOM manipulation and AJAX communication
- **Font Awesome**: Icon library
- **Chart.js**: Data visualization for impact metrics

#### Backend Technologies
- **PHP 8.0**: Server-side programming language
- **PDO (PHP Data Objects)**: Database abstraction layer
- **Session Management**: PHP native sessions
- **File Upload Handling**: PHP file I/O operations

#### Database
- **MySQL 5.7+**: Relational database management system
- **Normalization**: Third Normal Form (3NF)
- **Storage Engine**: InnoDB with foreign key constraints

### Architectural Pattern

The application follows the **Model-View-Controller (MVC)** architectural pattern:

- **Models** (`app/models/`): Data access layer and business logic
- **Views** (`public/*.php`): Presentation layer and user interface
- **Controllers** (`app/controllers/`): Request handling and application flow
- **Configuration** (`app/config/`): Database connections and application settings
- **Utilities** (`app/includes/`): Helper functions and shared components

---

## System Architecture

### Directory Structure

```
skillshare-local/
├─ public/                          # All files directly accessible via browser
│  ├─ index.php                     # Landing page + browse/search sessions
│  ├─ login.php                     # Login form + processing
│  ├─ register.php                  # Registration (learner/instructor)
│  ├─ sessions.php                  # List/browse/search skill sessions
│  ├─ session_view.php              # Single session details + Book Now
│  ├─ instructor_dashboard.php      # Manage sessions, bookings
│  ├─ learner_dashboard.php         # My bookings, rate sessions
│  ├─ admin_dashboard.php           # Admin area: users, stats
│  ├─ impact_dashboard.php          # Community impact dashboard
│  └─ assets/
│     ├─ css/
│     │  ├─ bootstrap.min.css
│     │  └─ style.css               # Custom CSS
│     ├─ js/
│     │  ├─ jquery.min.js
│     │  ├─ bootstrap.bundle.min.js
│     │  └─ main.js                 # Custom JS/jQuery
│     └─ images/
│        ├─ sessions/               # Uploaded session photos
│        └─ ui/                     # Logos, icons, etc.
│
├─ app/
│  ├─ config/
│  │  ├─ config.php                 # App settings
│  │  └─ database.php               # PDO/MySQL connection
│  ├─ includes/
│  │  ├─ header.php                 # HTML head, Bootstrap CSS
│  │  ├─ navbar.php                 # Top navigation (role-aware)
│  │  ├─ footer.php                 # Footer, scripts
│  │  ├─ auth_guard.php             # Authentication helpers
│  │  └─ helpers.php                # Utility functions
│  ├─ models/                       # Database logic
│  │  ├─ User.php
│  │  ├─ Session.php
│  │  ├─ Booking.php
│  │  ├─ Rating.php
│  │  └─ Impact.php                 # Environmental impact calculations
│  └─ controllers/                  # Request handling
│     ├─ AuthController.php
│     ├─ SessionController.php
│     ├─ BookingController.php
│     ├─ RatingController.php
│     └─ AdminController.php
│
├─ sql/
│  └─ schema.sql                    # Database schema
│
└─ docs/
   └─ TODO.md                       # Project progress tracking

```

### Component Description

#### Public Directory (`public/`)
Contains all publicly accessible files including page views, assets, and AJAX endpoints.

**Key Pages**:
- `index.php`: Landing page with session browsing and search functionality
- `login.php` / `register.php`: Authentication interfaces
- `dashboard.php`: Role-specific dashboard (learner/instructor/admin)
- `sessions.php`: Session listing with advanced filtering
- `session_view.php`: Detailed session information with booking interface
- `my_bookings.php`: User's booking history and management
- `rate_session.php`: Session rating and review submission
- `impact_dashboard.php`: Environmental impact visualization

**Assets**:
- `assets/css/`: Stylesheets (Bootstrap, custom themes)
- `assets/js/`: JavaScript files (jQuery, custom scripts)
- `assets/images/`: Static images and user-uploaded content

#### Application Directory (`app/`)
Contains server-side logic organized by the MVC pattern.

**Configuration** (`config/`):
- `database.php`: PDO database connection management
- `config.php`: Application-wide configuration constants
- `constants.php`: Centralized constant definitions
- `autoload.php`: Class autoloading implementation

**Models** (`models/`):
- `User.php`: User authentication and profile management
- `Session.php`: Workshop session CRUD operations
- `Booking.php`: Booking workflow and status management
- `Rating.php`: Rating and review functionality
- `Impact.php`: Environmental impact calculations
- `ImpactFactor.php`: Impact factor management
- `Settings.php`: User preferences and platform settings

**Controllers** (`controllers/`):
- `AuthController.php`: Authentication and authorization logic
- `SessionController.php`: Session management operations
- `BookingController.php`: Booking workflow coordination
- `RatingController.php`: Rating submission and retrieval

**Includes** (`includes/`):
- `header.php` / `navbar.php` / `footer.php`: Shared UI components
- `auth_guard.php`: Authentication middleware
- `helpers.php`: Utility functions
- `Validator.php`: Input validation class
- `Utils.php`: General utility functions
- `ErrorHandler.php`: Centralized error handling

#### Database Directory (`sql/`)
Contains database schema and seed data.

**Files**:
- `schema.sql`: Complete database structure (DDL)
- `settings_seed.sql`: Default platform settings
- `dummy_data.sql`: Sample sessions and categories for testing
- `test_users.sql`: Pre-configured test accounts

---

## Functional Requirements

### User Roles and Capabilities

#### 1. Learner Role
- ✓ Register account with profile information
- ✓ Browse and search available sessions
- ✓ Filter sessions by category, location, date, and fee type
- ✓ View detailed session information
- ✓ Book sessions (single or multiple seats)
- ✓ Manage personal bookings
- ✓ Rate and review completed sessions
- ✓ Customize user preferences (theme, language, timezone)

#### 2. Instructor Role
- ✓ All learner capabilities
- ✓ Create new skill-sharing sessions
- ✓ Edit and manage own sessions
- ✓ Upload session photos
- ✓ Accept or decline booking requests
- ✓ Cancel sessions with notifications
- ✓ View booking statistics
- ✓ Track session ratings and feedback

#### 3. Administrator Role
- ✓ All instructor capabilities
- ✓ Manage user accounts (suspend/activate)
- ✓ Modify user roles
- ✓ Oversee all sessions platform-wide
- ✓ Cancel any session
- ✓ View comprehensive platform statistics
- ✓ Manage environmental impact factors
- ✓ Configure platform settings
- ✓ Access booking analytics

### Core Features

#### Authentication System
- Secure user registration with role selection
- Password hashing using bcrypt algorithm
- Session-based authentication
- Role-based access control (RBAC)
- Password complexity validation
- Account suspension mechanism

#### Session Management
- CRUD operations for skill-sharing sessions
- Category classification system
- Location specification (in-person with address, online with meeting link)
- Capacity management and availability tracking
- Fee structure (free or paid with amount)
- Session status workflow (upcoming → completed → canceled)
- Image upload for session representation
- Advanced search and filtering capabilities

#### Booking System
- Multi-seat booking support
- Three-stage booking workflow:
  1. **Pending**: Initial booking request submitted
  2. **Accepted/Declined**: Instructor approval/rejection
  3. **Canceled**: User or instructor cancellation
- Capacity validation and overbooking prevention
- One booking per learner per session constraint
- Rejection reason documentation
- Real-time availability updates

#### Rating and Review System
- Five-star rating scale
- Written review submission
- Average rating calculation
- Rating statistics and distribution
- Validation: only completed sessions can be rated
- Prevention of duplicate ratings
- Aggregated ratings for instructors and sessions

#### Environmental Impact Tracking
- Configurable impact factors by category
- CO₂ savings calculation per session
- Community-wide impact aggregation
- Category-based impact breakdown
- Visual data representation using charts
- Historical impact tracking

#### User Preferences
- Theme selection (light/dark mode)
- Language preference
- Timezone configuration
- Currency selection
- AJAX-based preference persistence
- Cookie consent management

---

## Installation and Configuration

### System Requirements

#### Hardware Requirements
- **Processor**: 2.0 GHz dual-core or higher
- **RAM**: 4 GB minimum (8 GB recommended)
- **Storage**: 2 GB free disk space
- **Network**: Internet connection for downloading dependencies

#### Software Requirements

#### Software Requirements

**Required Software**:

**Required Software**:
1. **XAMPP** (v8.0 or higher) or equivalent LAMP/WAMP stack
   - Apache HTTP Server 2.4+
   - MySQL Database Server 5.7+ (or MariaDB 10.3+)
   - PHP 8.0+ with required extensions:
     - PDO MySQL extension
     - GD Library (for image processing)
     - FileInfo extension (for file uploads)
     - Session extension
     - JSON extension
2. **Modern Web Browser**:
   - Google Chrome 90+ (recommended)
   - Mozilla Firefox 88+
   - Microsoft Edge 90+
   - Safari 14+ (for macOS)
3. **Code Editor** (for viewing source):
   - Visual Studio Code (recommended)
   - Sublime Text
   - PhpStorm
   - Or any text editor
4. **Git** (for version control and cloning repository)

**Optional Software**:
- **Composer**: PHP dependency manager (for future enhancements)
- **Postman**: API testing (for AJAX endpoint testing)
- **MySQL Workbench**: Advanced database management

---

### Installation Procedure

#### Step 1: Environment Setup

**1.1 Install XAMPP**
- Download XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)
- Run the installer and follow installation wizard
- **Windows**: Install to `C:\xampp\`
- **macOS**: Install to `/Applications/XAMPP/`
- **Linux**: Install to `/opt/lampp/`

**1.2 Verify Installation**
```bash
# Check PHP version (must be 8.0+)
php -v

# Check MySQL service
mysql --version
```

**1.3 Start Services**
- Launch XAMPP Control Panel
- Start **Apache** service (port 80)
- Start **MySQL** service (port 3306)
- Verify green status indicators

#### Step 2: Obtain Project Files

**Option A: Clone Repository**
```bash
git clone https://github.com/ChrisPa691/SkillShare-Local.git
cd SkillShare-Local
```

**Option B: Download ZIP**
- Download project ZIP from repository
- Extract to temporary location
- Rename folder to `SkillShare-Local` (if needed)

#### Step 3: Deploy Application

**3.1 Move to Web Root**
- Copy `SkillShare-Local` folder to XAMPP's `htdocs` directory:
  - **Windows**: `C:\xampp\htdocs\SkillShare-Local`
  - **macOS**: `/Applications/XAMPP/htdocs/SkillShare-Local`
  - **Linux**: `/opt/lampp/htdocs/SkillShare-Local`

**3.2 Verify File Permissions**

**Windows**:
```powershell
# Ensure IIS_IUSRS or Apache user has read/write access
icacls "C:\xampp\htdocs\SkillShare-Local\public\uploads" /grant Users:F
```

**macOS/Linux**:
```bash
cd /Applications/XAMPP/htdocs/SkillShare-Local  # or /opt/lampp/htdocs/SkillShare-Local
sudo chmod -R 755 public/uploads
sudo chmod -R 755 logs
sudo chown -R daemon:daemon public/uploads  # macOS
# or
sudo chown -R www-data:www-data public/uploads  # Linux
```

#### Step 4: Database Configuration

**4.1 Access phpMyAdmin**
- Open web browser
- Navigate to: `http://localhost/phpmyadmin`
- Login with default credentials (usually no password for root)

**4.2 Create Database**
1. Click **"New"** in left sidebar
2. Database name: `skillshare_local`
3. Collation: Select **"utf8mb4_unicode_ci"**
4. Click **"Create"**

**4.3 Import Database Schema**

Import files in the following sequence (order is important):

1. **Import Schema** (table structure):
   - Click on `skillshare_local` database
   - Select **"Import"** tab
   - Click **"Choose File"**
   - Navigate to: `SkillShare-Local/sql/schema.sql`
   - Click **"Go"**
   - Verify success message: "Import has been successfully finished"

2. **Import Settings Data**:
   - Repeat import process for: `sql/settings_seed.sql`
   - Verify: Check that `UserSettings` table has default records

3. **Import Sample Data** (Optional - for testing):
   - Import: `sql/dummy_data.sql` (sample sessions and categories)
   - Import: `sql/test_users.sql` (pre-configured test accounts)

**4.4 Verify Database Structure**
- Confirm the following tables exist:
  - `Users` (user accounts)
  - `Categories` (session categories)
  - `Sessions` (skill-sharing workshops)
  - `Bookings` (booking requests)
  - `Ratings` (session reviews)
  - `ImpactFactors` (environmental impact data)
  - `UserSettings` (user preferences)

#### Step 5: Application Configuration

**5.1 Configure Database Connection**
- Open: `app/config/database.php`
- Verify/update connection parameters:

```php
<?php
// Database Configuration
$host = 'localhost';        // MySQL server (localhost for XAMPP)
$dbname = 'skillshare_local';  // Database name
$username = 'root';         // MySQL username (default: root)
$password = '';             // MySQL password (default: empty for XAMPP)
$charset = 'utf8mb4';       // Character encoding

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
```

**5.2 Create Required Directories**

Ensure these directories exist:
```bash
SkillShare-Local/
├─ public/uploads/
│  ├─ avatars/      # User profile photos
│  └─ sessions/     # Session photos
└─ logs/            # Application error logs
```

**Create directories** (if they don't exist):
```bash
cd SkillShare-Local
mkdir -p public/uploads/avatars
mkdir -p public/uploads/sessions
mkdir -p logs
```

**5.3 Configure PHP Settings**

Edit PHP configuration (`C:\xampp\php\php.ini` on Windows):

```ini
; File Upload Configuration
file_uploads = On
upload_max_filesize = 10M
post_max_size = 12M
max_file_uploads = 20

; Execution Time
max_execution_time = 300
max_input_time = 300

; Error Reporting (Development Environment)
display_errors = On
display_startup_errors = On
error_reporting = E_ALL

; Session Configuration
session.save_handler = files
session.save_path = "C:/xampp/tmp"  # Adjust for your OS
session.gc_maxlifetime = 1440

; Timezone
date.timezone = Europe/Athens  # Or your timezone
```

**Restart Apache** after modifying `php.ini`

#### Step 6: Verification and Testing

**6.1 Access Application**
- Open web browser
- Navigate to: `http://localhost/SkillShare-Local/public/`
- Expected result: SkillShare Local landing page loads successfully

**6.2 Test Database Connection**
- If the page loads without errors, database connection is successful
- If you see connection errors, review Step 4 and Step 5

**6.3 Test User Authentication**

If you imported `test_users.sql`, test with these credentials:

| Role | Email | Password | Purpose |
|------|-------|----------|---------|
| Administrator | `admin@skillshare.local` | `Admin123!` | Platform management testing |
| Instructor | `instructor@skillshare.local` | `Instructor123!` | Session creation testing |
| Learner | `learner@skillshare.local` | `Learner123!` | Booking workflow testing |

**6.4 Test Core Functionality**
1. **Registration**: Create new user account
2. **Login**: Authenticate with test credentials
3. **Session Browsing**: View available sessions
4. **Booking** (as learner): Book a session
5. **Session Creation** (as instructor): Create new session
6. **Admin Panel** (as admin): Access admin dashboard

---

### Advanced Configuration

#### Enable URL Rewriting (Optional)

For cleaner URLs without `.php` extension:

**Create `.htaccess` file** in `public/` directory:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /SkillShare-Local/public/
    
    # Remove .php extension from URLs
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME}.php -f
    RewriteRule ^(.+)$ $1.php [L,QSA]
    
    # Redirect to HTTPS (if SSL is configured)
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

**Enable mod_rewrite** in Apache:
- Edit `httpd.conf` (XAMPP: `C:\xampp\apache\conf\httpd.conf`)
- Find and uncomment: `LoadModule rewrite_module modules/mod_rewrite.so`
- Find `<Directory>` section for htdocs
- Change `AllowOverride None` to `AllowOverride All`
- Restart Apache

#### Configure Error Logging

**Production Environment Configuration**:

Edit `app/includes/ErrorHandler.php`:
```php
// Disable display, enable logging
ErrorHandler::init(__DIR__ . '/../../logs/errors.log', false);
```

Edit `php.ini`:
```ini
; Production Settings
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = "C:/xampp/htdocs/SkillShare-Local/logs/php_errors.log"
```

#### Configure Email (Future Enhancement)

For password reset and notifications:
```php
// In app/config/config.php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
```

---

## Usage Guide

### For Learners

1. **Account Creation**:
   - Navigate to registration page
   - Select "Learner" role
   - Complete profile information
   - Upload profile photo (optional)

2. **Discovering Sessions**:
   - Browse all sessions on home page
   - Use search functionality
   - Apply filters (category, location, date range, fee type)
   - View detailed session information

3. **Booking Process**:
   - Click "Book Now" on desired session
   - Select number of seats
   - Submit booking request
   - Wait for instructor confirmation
   - Receive notification of acceptance/rejection

4. **Managing Bookings**:
   - View all bookings in "My Bookings"
   - Track booking status (pending/accepted/declined)
   - Cancel bookings if needed

5. **Rating Sessions**:
   - After session completion, click "Rate Session"
   - Provide star rating (1-5)
   - Write review (optional)
   - Submit feedback

### For Instructors

1. **Session Creation**:
   - Navigate to "Create Session" page
   - Fill in session details:
     - Title and description
     - Category selection
     - Location (in-person address or online link)
     - Date, time, and duration
     - Capacity and fee structure
   - Upload representative photo
   - Publish session

2. **Managing Sessions**:
   - View all created sessions in dashboard
   - Edit session details
   - Cancel sessions with reason
   - Monitor available capacity

3. **Handling Bookings**:
   - Review incoming booking requests
   - Accept or decline requests
   - Provide rejection reason (if declining)
   - Track accepted bookings

4. **Monitoring Performance**:
   - View session ratings and reviews
   - Analyze booking statistics
   - Track environmental impact contributions

### For Administrators

1. **User Management**:
   - View all registered users
   - Search and filter users
   - Suspend/activate accounts
   - Modify user roles
   - Monitor user activity

2. **Content Moderation**:
   - Oversee all sessions platform-wide
   - Cancel inappropriate sessions
   - Review booking activities
   - Manage reported content

3. **Platform Configuration**:
   - Configure environmental impact factors
   - Set platform-wide settings
   - Manage categories
   - Monitor system health

4. **Analytics and Reporting**:
   - View platform statistics:
     - Total users by role
     - Session completion rates
     - Booking acceptance rates
     - Popular categories
   - Review environmental impact metrics
   - Generate usage reports

---

## Troubleshooting

### Common Issues and Solutions

#### Issue 1: Database Connection Failure

**Symptoms**:
- Error message: "Database connection failed"
- Blank page on first load
- PDO connection exceptions

**Solutions**:
1. Verify MySQL service is running in XAMPP Control Panel
2. Check database credentials in `app/config/database.php`
3. Confirm database `skillshare_local` exists in phpMyAdmin
4. Test MySQL connection:
   ```bash
   mysql -u root -p
   # If prompted for password, press Enter (default is no password)
   SHOW DATABASES;
   USE skillshare_local;
   SHOW TABLES;
   ```
5. Check for port conflicts (MySQL default port: 3306)

#### Issue 2: 404 Not Found Errors

**Symptoms**:
- Pages return "404 Not Found"
- CSS/JavaScript files not loading
- Images not displaying

**Solutions**:
1. Verify project location: must be in `htdocs/SkillShare-Local/`
2. Check Apache service is running
3. Confirm URL path: `http://localhost/SkillShare-Local/public/index.php`
4. Review Apache error log: `C:\xampp\apache\logs\error.log`
5. Check file permissions (ensure Apache can read files)

#### Issue 3: White Screen / Blank Page

**Symptoms**:
- Page loads but displays nothing
- No error messages visible
- Browser shows white screen

**Solutions**:
1. Enable PHP error display:
   ```php
   // Add to top of index.php temporarily
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
2. Check PHP error log: `C:\xampp\php\logs\php_error_log`
3. Verify PHP version: `php -v` (must be 8.0+)
4. Check for syntax errors in PHP files
5. Review browser console (F12) for JavaScript errors

#### Issue 4: File Upload Failures

**Symptoms**:
- "Failed to upload file" error
- Uploaded images not displaying
- Profile photo update fails

**Solutions**:
1. Verify directory exists and permissions:
   ```bash
   ls -la public/uploads/  # macOS/Linux
   dir public\uploads\     # Windows
   ```
2. Check `php.ini` upload settings:
   ```ini
   file_uploads = On
   upload_max_filesize = 10M
   post_max_size = 12M
   ```
3. Ensure sufficient disk space
4. Verify file type restrictions in code
5. Check temporary upload directory in `php.ini`:
   ```ini
   upload_tmp_dir = "C:/xampp/tmp"
   ```

#### Issue 5: Session/Login Problems

**Symptoms**:
- Cannot log in with correct credentials
- Logged out unexpectedly
- Session data not persisting

**Solutions**:
1. Clear browser cookies and cache
2. Verify session configuration in `php.ini`:
   ```ini
   session.save_path = "C:/xampp/tmp"
   session.gc_maxlifetime = 1440
   ```
3. Check session directory exists and is writable
4. Review session initialization in code
5. Test with different browser (eliminate browser-specific issues)

#### Issue 6: AJAX Functionality Not Working

**Symptoms**:
- Preferences not saving
- Search/filter not updating
- No response from AJAX calls

**Solutions**:
1. Open browser Developer Tools (F12)
2. Check Console tab for JavaScript errors
3. Review Network tab for failed requests:
   - Status codes (200 = success, 404 = not found, 500 = server error)
   - Response data
4. Verify jQuery is loaded:
   ```javascript
   // In browser console
   typeof jQuery  // Should return "function"
   ```
5. Check AJAX endpoint paths in JavaScript
6. Review PHP AJAX handler files for errors

#### Issue 7: Styling/CSS Issues

**Symptoms**:
- Page layout broken
- Bootstrap styles not applied
- Custom CSS not loading

**Solutions**:
1. Hard refresh page: `Ctrl + F5` (Windows) or `Cmd + Shift + R` (Mac)
2. Clear browser cache completely
3. Check Network tab (F12) for failed CSS requests
4. Verify CSS file paths in HTML
5. Ensure Bootstrap CDN is accessible (check internet connection)
6. Review custom CSS for syntax errors

---

## Testing Methodology

### Test Accounts

The system includes pre-configured test accounts for comprehensive testing:

| Role          | Username                   | Password       | Test Scenarios                              |
|---------------|----------------------------|----------------|---------------------------------------------|
| Administrator | admin@skillshare.local     | Admin123!      | User management, platform oversight         |
| Instructor    | instructor@skillshare.local| Instructor123! | Session creation, booking management        |
| Learner       | learner@skillshare.local   | Learner123!    | Session booking, rating submission          |

### Test Cases

#### Authentication Testing
- [x] User registration with valid data
- [x] Registration validation (email format, password strength)
- [x] Login with correct credentials
- [x] Login failure with incorrect credentials
- [x] Logout functionality
- [x] Session persistence across pages
- [x] Unauthorized access prevention

#### Session Management Testing
- [x] Create session with all required fields
- [x] Edit existing session
- [x] Delete/cancel session
- [x] Upload session photo
- [x] Search sessions by keyword
- [x] Filter by category, location, date, fee type
- [x] View session details
- [x] Capacity tracking accuracy

#### Booking Workflow Testing
- [x] Submit booking request
- [x] Instructor accept booking
- [x] Instructor decline booking with reason
- [x] Learner cancel booking
- [x] Prevent overbooking
- [x] Prevent duplicate bookings
- [x] Capacity update after acceptance
- [x] Status transitions (pending → accepted/declined)

#### Rating System Testing
- [x] Submit rating for completed session
- [x] Prevent rating before completion
- [x] Prevent duplicate ratings
- [x] Average rating calculation
- [x] Display rating statistics
- [x] Review comment submission

#### Admin Dashboard Testing
- [x] View all users
- [x] Search and filter users
- [x] Suspend user account
- [x] Modify user role
- [x] View platform statistics
- [x] Manage impact factors
- [x] Override session management

#### Responsive Design Testing
- [x] Mobile view (< 768px)
- [x] Tablet view (768px - 991px)
- [x] Desktop view (≥ 992px)
- [x] Navigation menu on mobile
- [x] Touch-friendly buttons
- [x] Image scaling

#### Cross-Browser Testing
- [x] Google Chrome (Windows, macOS)
- [x] Mozilla Firefox (Windows, macOS)
- [x] Microsoft Edge (Windows)
- [x] Safari (macOS, iOS)
- [x] Mobile browsers (iOS Safari, Chrome Mobile)

---

## Database Schema

### Entity-Relationship Overview

The database follows Third Normal Form (3NF) with the following entities:

1. **Users**: Platform users (learners, instructors, admins)
2. **Categories**: Session categories for classification
3. **Sessions**: Skill-sharing workshops
4. **Bookings**: Session booking requests and confirmations
5. **Ratings**: User reviews of completed sessions
6. **ImpactFactors**: Environmental impact metrics by category
7. **UserSettings**: User preference configurations

### Key Relationships

- **Users** → **Sessions** (1:N): One instructor creates many sessions
- **Users** → **Bookings** (1:N): One learner makes many bookings
- **Sessions** → **Bookings** (1:N): One session has many bookings
- **Sessions** → **Ratings** (1:N): One session has many ratings
- **Users** → **Ratings** (1:N): One user submits many ratings
- **Categories** → **Sessions** (1:N): One category contains many sessions
- **Categories** → **ImpactFactors** (1:N): One category has many impact factors

For detailed schema documentation, see [DATABASE-SCHEMA.md](DATABASE-SCHEMA.md).

---

## Project Management

### Development Timeline

**Phase 1: Foundation** (Weeks 1-3)
- Database design and normalization
- Basic authentication system
- MVC architecture setup
- Initial user interface

**Phase 2: Core Features** (Weeks 4-7)
- Session management (CRUD operations)
- Booking workflow implementation
- Rating system development
- File upload functionality

**Phase 3: Advanced Features** (Weeks 8-10)
- Admin dashboard and management tools
- Environmental impact tracking
- Search and filtering systems
- AJAX enhancements

**Phase 4: Polish and Testing** (Weeks 11-13)
- UI/UX improvements
- Code quality refactoring
- Comprehensive testing
- Documentation completion
- Bug fixes and optimization

### Course Requirements Fulfillment

#### ACSC476 Course Objectives

✅ **Objective 1**: Design and implement dynamic web applications
- Full-stack application with frontend and backend integration
- Database-driven content management
- User interaction and state management

✅ **Objective 2**: Utilize server-side programming
- PHP 8.0 with object-oriented programming
- MVC architectural pattern
- PDO for database abstraction
- Session and authentication management

✅ **Objective 3**: Implement database integration
- MySQL relational database design
- Normalized schema (3NF)
- Complex SQL queries with joins
- Transaction management

✅ **Objective 4**: Create responsive user interfaces
- Bootstrap 5 responsive framework
- Mobile-first design approach
- Cross-browser compatibility
- Accessibility considerations

✅ **Objective 5**: Apply security best practices
- Password hashing (bcrypt)
- SQL injection prevention (prepared statements)
- XSS mitigation (output escaping)
- Authentication and authorization
- File upload validation

✅ **Objective 6**: Develop professional documentation
- Comprehensive README (academic style)
- Database schema documentation
- Code comments and PHPDoc
- User guide and troubleshooting
- Changelog and version history

---

## Technologies Used

### Frontend Technologies
- **HTML5**: Semantic markup, accessibility features
- **CSS3**: Custom styling, animations, responsive design
- **Bootstrap 5.3**: Grid system, components, utilities
- **JavaScript (ES6+)**: Client-side logic, form validation
- **jQuery 3.6**: DOM manipulation, AJAX communication
- **Font Awesome 6**: Icon library for UI enhancement
- **Chart.js**: Data visualization for analytics

### Backend Technologies
- **PHP 8.0+**: Server-side programming language
- **PDO**: Database abstraction and prepared statements
- **Sessions**: User authentication and state management
- **File I/O**: Upload handling and image processing

### Database
- **MySQL 5.7+**: Relational database management
- **InnoDB**: Storage engine with ACID compliance
- **Triggers**: Automated data integrity (future enhancement)
- **Indexes**: Query optimization

### Development Tools
- **XAMPP**: Local development environment
- **Visual Studio Code**: Source code editor
- **phpMyAdmin**: Database management interface
- **Git**: Version control system
- **GitHub**: Code repository hosting
- **Chrome DevTools**: Debugging and testing

### Design Patterns and Principles
- **MVC**: Model-View-Controller architecture
- **OOP**: Object-oriented programming
- **DRY**: Don't Repeat Yourself principle
- **SOLID**: Single responsibility, open/closed, etc.
- **3NF**: Third Normal Form database normalization

---

## Future Enhancements

### Planned Features
1. **Email Notifications**
   - Booking confirmations
   - Session reminders
   - Password reset functionality

2. **Payment Integration**
   - Stripe/PayPal API integration
   - Fee collection for paid sessions
   - Refund management

3. **Advanced Analytics**
   - Instructor performance metrics
   - User engagement tracking
   - Revenue reports

4. **Social Features**
   - User profiles and bios
   - Follower system
   - Session sharing on social media

5. **Mobile Application**
   - Native iOS app
   - Native Android app
   - Push notifications

6. **API Development**
   - RESTful API endpoints
   - API documentation
   - Third-party integrations

---

## Known Limitations

1. **Email Functionality**: Currently not implemented; notifications are in-app only
2. **Payment Processing**: Paid sessions require manual payment arrangements
3. **Real-time Notifications**: No WebSocket implementation; requires page refresh
4. **Advanced Search**: Full-text search not optimized for very large datasets
5. **Scalability**: Current architecture suitable for small to medium deployments
6. **Internationalization**: UI prepared but translations not complete

---

## Academic Integrity Statement

This project was developed independently as part of the ACSC476 Web Programming course requirements. All code, documentation, and database design are original work by the student, with the exception of:

- **External Libraries**: Bootstrap, jQuery, Font Awesome, Chart.js (properly attributed)
- **Learning Resources**: PHP documentation, MySQL documentation, MDN Web Docs
- **Code Snippets**: Any adapted code from online resources is properly commented

The project adheres to Frederick University's academic integrity policies and represents the student's own work and understanding of web development concepts.

---

## References

### Technical Documentation
1. **PHP Manual**: [https://www.php.net/manual/en/](https://www.php.net/manual/en/)
2. **MySQL Documentation**: [https://dev.mysql.com/doc/](https://dev.mysql.com/doc/)
3. **Bootstrap Documentation**: [https://getbootstrap.com/docs/5.3/](https://getbootstrap.com/docs/5.3/)
4. **jQuery Documentation**: [https://api.jquery.com/](https://api.jquery.com/)
5. **MDN Web Docs**: [https://developer.mozilla.org/](https://developer.mozilla.org/)

### Learning Resources
1. Course lecture materials and slides
2. PHP: The Right Way - [https://phptherightway.com/](https://phptherightway.com/)
3. W3Schools Web Development Tutorials
4. Stack Overflow community discussions

### Standards and Best Practices
1. PSR-12: Extended Coding Style Guide
2. OWASP Web Security Guidelines
3. Web Content Accessibility Guidelines (WCAG) 2.1
4. RESTful API Design Principles

---

## Appendices

### Appendix A: File Structure Reference

Complete file listing with descriptions available in project documentation.

### Appendix B: Database ERD

Entity-Relationship Diagram available in [DATABASE-SCHEMA.md](DATABASE-SCHEMA.md).

### Appendix C: API Endpoints

AJAX endpoint documentation available in code comments.

### Appendix D: User Interface Screenshots

Screenshots of key interfaces available in `docs/screenshots/` directory.

---

## License and Usage

**Academic Use Only**

This project is developed for educational purposes as part of university coursework. 

**Copyright © 2026 Christos Paparistodimou**

### Permitted Use:
- ✓ Viewing for educational purposes
- ✓ Review by course instructors and evaluators
- ✓ Personal learning and reference

### Prohibited Use:
- ✗ Commercial use without permission
- ✗ Submission as original work by others (plagiarism)
- ✗ Redistribution without attribution

---

## Contact Information

**Student**: Christos Paparistodimou  
**Student ID**: [Your Student ID]  
**Email**: [your.email@student.frederick.ac.cy]  
**Course**: ACSC476 - Web Programming  
**Institution**: Frederick University  
**Department**: Department of Computer Science  
**Academic Year**: 2025-2026

**Project Repository**: [https://github.com/ChrisPa691/SkillShare-Local](https://github.com/ChrisPa691/SkillShare-Local)

---

## Acknowledgments

### Course Instruction
Special thanks to the ACSC476 Web Programming course instructor and teaching assistants for guidance and support throughout the development process.

### Resources and Tools
Gratitude to the developers and maintainers of open-source projects and tools used in this application:
- Bootstrap team for the responsive framework
- jQuery Foundation for the JavaScript library
- PHP development team
- MySQL/Oracle Corporation
- XAMPP developers (Apache Friends)

### Inspiration
This project was inspired by the growing need for sustainable community learning and the principles of the sharing economy.

---

**Document Version**: 1.0.0  
**Last Updated**: January 2026  
**Prepared for**: ACSC476 Web Programming Course Submission  
**Status**: Production Ready ✅

---

*End of README Document*
