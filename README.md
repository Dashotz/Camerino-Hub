# Gov D.M. Camerino School Management System

A comprehensive web-based Learning Management System (LMS) for Gov D.M. Camerino High School, designed to streamline educational processes and enhance communication between students, teachers, and administrators. Built with pure PHP (no frameworks) for maximum flexibility and control.

## 🎯 Features

### For Students
- **Dashboard**: Personalized academic dashboard with overview of courses, assignments, and grades
- **Course Management**: View enrolled classes, subjects, and course materials
- **Assignments & Activities**: Submit assignments and activities with file uploads
- **Quiz System**: Take online quizzes with anti-cheat protection
- **Grades Viewing**: Detailed grade breakdown by subject and activity
- **Attendance Tracking**: View attendance records
- **Calendar**: Track events, assignments, and deadlines
- **Announcements**: Stay updated with school and class announcements
- **Profile Management**: Update personal information and change password
- **Notifications**: Real-time notification system
- **Search**: Search functionality for courses and content
- **Site Map**: Navigate school facilities and information

### For Teachers
- **Class Management**: Create and manage classes with student rosters
- **Subject Management**: Add subjects to classes and manage course content
- **Assignment Creation**: Create assignments with due dates, points, and file attachments
- **Activity Management**: Create various types of activities (assignments, quizzes, etc.)
- **Quiz Builder**: Create quizzes with multiple question types, images, and time limits
- **Grade Management**: Input and update grades for assignments, quizzes, and activities
- **Attendance Tracking**: Record and export student attendance with date ranges
- **Student Progress**: Monitor individual student progress and performance
- **Announcement System**: Post class and school-wide announcements
- **Submission Management**: View, grade, and provide feedback on student submissions
- **Reports**: Generate attendance and grade reports
- **Backup & Restore**: System backup and restore functionality
- **Profile Management**: Update teacher profile and security settings

### For Administrators
- **User Management**: 
  - Manage student accounts (add, edit, archive, import from CSV)
  - Manage teacher accounts (add, edit, archive, activate/deactivate)
- **Section Management**: Create and manage class sections
- **Subject Management**: Add, edit, and manage subjects
- **Academic Year Settings**: Configure academic terms and periods
- **Report Generation**: Generate various academic and system reports
- **Backup & Restore**: Automated system backup and restore functionality
- **Support System**: Manage support tickets and inquiries
- **System Settings**: Configure system-wide settings
- **Archived Records**: Manage archived students and teachers with auto-deletion cron jobs

## 🛠️ Technologies Used

### Backend
- **PHP**: Pure PHP (no frameworks) - File-based routing
- **MySQL**: Database management system
- **Session Management**: PHP sessions for authentication

### Frontend
- **HTML5**: Semantic markup
- **CSS3**: Custom styling with separate CSS files per module
- **JavaScript**: Vanilla JS and jQuery for interactivity
- **Bootstrap 4**: Responsive UI framework
- **Font Awesome 6.4.0**: Icon library
- **SweetAlert2**: Modern alert dialogs

### PHP Libraries (via Composer)
- **tecnickcom/tcpdf** (^6.6): PDF generation for reports and exports
- **phpoffice/phpspreadsheet** (^3.6): Excel file handling for imports/exports
- **phpmailer/phpmailer** (^6.9): Email functionality
- **smalot/pdfparser** (^2.11): PDF parsing capabilities

### Additional Libraries
- jQuery
- Popper.js
- Bootstrap 4

## 🚀 Getting Started

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB equivalent)
- Web server (Apache/Nginx)
- Composer (for dependency management)
- PHP extensions: mysqli, mbstring, gd, zip

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/camerino-school-system.git
   cd camerino-school-system
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Configure base URL**
   - Edit `config/config.php` and update the `BASE_URL` constant to match your installation path:
     ```php
     define('BASE_URL', '/your-installation-path/');
     ```

4. **Set up file permissions**
   - Ensure `uploads/` directory and subdirectories are writable:
     ```bash
     chmod -R 755 uploads/
     ```
   - Ensure `backups/` directory is writable:
     ```bash
     chmod -R 755 backups/
     ```

5. **Configure web server**
   - Point your web server document root to the project directory
   - For Apache, ensure mod_rewrite is enabled (if using .htaccess)
   - For Nginx, configure proper PHP-FPM settings

6. **Set up cron jobs (optional)**
   - For automated archive deletion, set up a cron job:
     ```bash
     0 0 * * * php /path/to/admin/cron/auto_delete_archived.php
     ```

## 📁 Project Structure

```
Camerino-Hub/
├── admin/                    # Administrator portal
│   ├── handlers/            # Request handlers for admin operations
│   ├── includes/            # Reusable admin components (header, footer, sidebar)
│   ├── functions/           # Admin-specific functions
│   ├── cron/                # Scheduled tasks (auto-delete archived records)
│   └── *.php                # Admin pages (dashboard, user management, etc.)
│
├── Student/                 # Student portal
│   ├── handlers/           # Request handlers for student operations
│   ├── includes/           # Reusable student components
│   ├── css/                # Student-specific stylesheets
│   ├── js/                 # Student-specific JavaScript
│   └── *.php               # Student pages (dashboard, courses, assignments, etc.)
│
├── Teacher/                 # Teacher portal
│   ├── handlers/           # Request handlers for teacher operations
│   ├── includes/           # Reusable teacher components
│   ├── css/                # Teacher-specific stylesheets
│   ├── js/                 # Teacher-specific JavaScript
│   └── *.php               # Teacher pages (dashboard, classes, grading, etc.)
│
├── config/                  # Configuration files
│   └── config.php          # Base URL and path configurations
│
├── db/                      # Database layer
│   └── dbConnector.php     # Database connection class
│
├── handlers/                # Shared request handlers
│
├── images/                  # Image assets (logos, illustrations, etc.)
│
├── uploads/                 # User-uploaded files
│   ├── announcements/      # Announcement attachments
│   ├── quiz_images/        # Quiz question images
│   ├── students/           # Student profile pictures
│   └── results/            # Generated reports and exports
│
├── backups/                 # System backup files
│
├── vendor/                  # Composer dependencies (auto-generated)
│
├── login.php               # Main login page (unified for all user types)
├── login_action.php        # Login authentication handler
├── forgot-password.php     # Password recovery page
├── composer.json           # PHP dependencies
└── README.md               # This file
```

## 🏗️ Architecture

This is a **pure PHP application** with no frameworks. The architecture follows a traditional file-based routing pattern:

- **Entry Point**: `login.php` serves as the main entry point
- **Routing**: File-based routing (each page is a separate PHP file)
- **Authentication**: Session-based authentication with role checking
- **Database Layer**: Custom database connector class with prepared statements for security
- **Separation of Concerns**: 
  - Pages in root directories (`admin/`, `Student/`, `Teacher/`)
  - Handlers in `handlers/` subdirectories for AJAX/API requests
  - Shared includes in `includes/` subdirectories
  - Configuration in `config/` directory

## 🔒 Security Features

- **Session Management**: PHP sessions with proper session handling
- **Password Hashing**: MD5 hashing (consider upgrading to bcrypt/argon2)
- **SQL Injection Prevention**: Prepared statements via `DbConnector::prepare()`
- **XSS Protection**: Input sanitization and output escaping
- **File Upload Security**: File type validation and secure storage
- **Anti-Cheat System**: Quiz anti-cheat protection for students
- **Role-Based Access Control**: Separate authentication for students, teachers, and admins
- **Input Validation**: Server-side validation for all user inputs
- **Error Handling**: Error logging without exposing sensitive information

## 🔄 Backup & Restore

The system includes automated backup functionality:
- Manual backup creation via admin panel
- Scheduled backups (via cron)
- Restore functionality with backup history
- Backup files stored in `backups/` directory

## 📱 Mobile Support

- Responsive design with Bootstrap 4
- Mobile-friendly interface for all user roles
- Android mobile app available (download link in login page)

## ✨ Future Enhancements

- [ ] Upgrade password hashing from MD5 to bcrypt/argon2
- [ ] Implement RESTful API structure
- [ ] Add unit and integration tests
- [ ] Implement proper CSRF token protection
- [ ] Add API documentation
- [ ] Enhanced mobile application features
- [ ] Parent portal implementation
- [ ] Library management system
- [ ] Advanced analytics and reporting
- [ ] Real-time notifications (WebSocket)
- [ ] Multi-language support

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Contact

**Gov D.M. Camerino High School**
- **Address**: Medicion 2, Imus City, Cavite 4103, Philippines

## 🏫 About

This system was developed to support the educational initiatives of Gov D.M. Camerino High School, in partnership with:
- Department of Education (DepEd)
- Bagong Pilipinas Initiative
- City of Imus
- Province of Cavite

---

Made with ❤️ for Gov D.M. Camerino High School
