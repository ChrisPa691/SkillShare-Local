# SkillShare Local

## Description

SkillShare Local is a university project developed for the ACSC476 Web Programming course. It is a web-based platform that connects instructors who offer eco-friendly skill workshops with learners interested in sustainability. The system allows instructors to create and manage sessions, while learners can browse, book, and rate completed workshops.

The project is built using HTML5, CSS3, Bootstrap, JavaScript with jQuery, PHP, and MySQL. Core features include user registration and login, session publishing, booking and confirmation workflows, ratings, and an admin dashboard for account management and sustainability impact visualization. The platform also computes estimated environmental benefits (e.g., CO₂ savings) based on session participation, supporting community awareness around sustainable practices.

## Project Structure

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

## Setup Instructions

### Prerequisites

- **XAMPP** (or similar local server with Apache and MySQL)
  - PHP 7.4 or higher
  - MySQL 5.7 or higher
- **Web Browser** (Chrome, Firefox, Edge, or Safari)
- **Git** (for cloning the repository)

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/ChrisPa691/SkillShare-Local.git
   cd SkillShare-Local
   ```

2. **Move to XAMPP htdocs**
   - Copy or move the project folder to your XAMPP `htdocs` directory
   - Typical path: `C:\xampp\htdocs\SkillShare-Local` (Windows) or `/Applications/XAMPP/htdocs/SkillShare-Local` (Mac)

3. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start **Apache** and **MySQL** services

4. **Create Database**
   - Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
   - Create a new database named `skillshare_local`
   - Import the database schema:
     - Click on the `skillshare_local` database
     - Go to the "Import" tab
     - Choose `sql/schema.sql` file from the project
     - Click "Go" to execute

5. **Configure Database Connection**
   - Open `app/config/database.php`
   - Update the database credentials if needed:
     ```php
     // Default XAMPP settings
     $host = 'localhost';
     $dbname = 'skillshare_local';
     $username = 'root';
     $password = '';  // Empty for default XAMPP
     ```

6. **Access the Application**
   - Open your web browser
   - Navigate to: `http://localhost/SkillShare-Local/public/`
   - You should see the landing page

7. **Create Admin Account** (Optional)
   - Register a new account through the registration page
   - Manually update the user role in the database to 'admin' via phpMyAdmin if needed

### Troubleshooting

- **404 Error**: Make sure the project is in the `htdocs` folder and Apache is running
- **Database Connection Error**: Verify database credentials in `app/config/database.php`
- **Permission Issues**: Ensure the `public/assets/images/sessions/` directory has write permissions for file uploads

## Technologies Used

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript, jQuery
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Architecture**: MVC (Model-View-Controller)
- **Version Control**: Git & GitHub

## Features

- User authentication (registration/login) with role-based access
- Session management (create, edit, delete for instructors)
- Browse and search skill sessions
- Booking system with confirmation workflow
- Rating and review system for completed sessions
- Admin dashboard for user and content management
- Environmental impact tracking and visualization
- Responsive design for mobile and desktop

## Contributors

- ChrisPa691

## License

This is a university course project developed for educational purposes.

---

**Course**: ACSC476 - Web Programming  
**Institution**: [Your University Name]  
**Semester**: Fall 2025
