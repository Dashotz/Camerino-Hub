<div align="center">

# 🎓 Gov D.M. Camerino School Management System

### A Comprehensive Learning Management System (LMS)

[![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-4-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

**Empowering education through digital innovation**

[Features](#-features) • [Installation](#-getting-started) • [Documentation](#-project-structure) • [Contact](#-contact)

---

</div>

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
  - [👨‍🎓 For Students](#-for-students)
  - [👨‍🏫 For Teachers](#-for-teachers)
  - [👨‍💼 For Administrators](#-for-administrators)
- [Tech Stack](#-tech-stack)
- [Getting Started](#-getting-started)
- [Project Structure](#-project-structure)
- [Architecture](#-architecture)
- [Security](#-security-features)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🎯 Overview

**Gov D.M. Camerino School Management System** is a comprehensive web-based Learning Management System designed to streamline educational processes and enhance communication between students, teachers, and administrators at Gov D.M. Camerino High School.

Built with **pure PHP** (no frameworks) for maximum flexibility and control, this system provides a robust, secure, and user-friendly platform for managing all aspects of school operations.

### ✨ Key Highlights

- 🎯 **Role-Based Access Control** - Separate portals for Students, Teachers, and Administrators
- 📱 **Responsive Design** - Mobile-friendly interface for all devices
- 🔒 **Security First** - Multiple layers of security protection
- 📊 **Comprehensive Reporting** - Generate detailed academic and system reports
- 🔄 **Automated Backups** - Scheduled database backups with restore functionality
- 📝 **Quiz System** - Advanced quiz builder with anti-cheat protection
- 📧 **Notification System** - Real-time notifications for all users

---

## 🎯 Features

### 👨‍🎓 For Students

| Feature | Description |
|---------|-------------|
| 📊 **Dashboard** | Personalized academic dashboard with overview of courses, assignments, and grades |
| 📚 **Course Management** | View enrolled classes, subjects, and course materials |
| 📝 **Assignments & Activities** | Submit assignments and activities with file uploads |
| 🧪 **Quiz System** | Take online quizzes with anti-cheat protection |
| 📈 **Grades Viewing** | Detailed grade breakdown by subject and activity |
| ✅ **Attendance Tracking** | View attendance records |
| 📅 **Calendar** | Track events, assignments, and deadlines |
| 📢 **Announcements** | Stay updated with school and class announcements |
| 👤 **Profile Management** | Update personal information and change password |
| 🔔 **Notifications** | Real-time notification system |
| 🔍 **Search** | Search functionality for courses and content |
| 🗺️ **Site Map** | Navigate school facilities and information |

### 👨‍🏫 For Teachers

| Feature | Description |
|---------|-------------|
| 🏫 **Class Management** | Create and manage classes with student rosters |
| 📖 **Subject Management** | Add subjects to classes and manage course content |
| 📝 **Assignment Creation** | Create assignments with due dates, points, and file attachments |
| 🎯 **Activity Management** | Create various types of activities (assignments, quizzes, etc.) |
| 🧪 **Quiz Builder** | Create quizzes with multiple question types, images, and time limits |
| 📊 **Grade Management** | Input and update grades for assignments, quizzes, and activities |
| ✅ **Attendance Tracking** | Record and export student attendance with date ranges |
| 📈 **Student Progress** | Monitor individual student progress and performance |
| 📢 **Announcement System** | Post class and school-wide announcements |
| 📤 **Submission Management** | View, grade, and provide feedback on student submissions |
| 📄 **Reports** | Generate attendance and grade reports |
| 💾 **Backup & Restore** | System backup and restore functionality |
| ⚙️ **Profile Management** | Update teacher profile and security settings |

### 👨‍💼 For Administrators

| Feature | Description |
|---------|-------------|
| 👥 **User Management** | Manage student and teacher accounts (add, edit, archive, import from CSV) |
| 🏛️ **Section Management** | Create and manage class sections |
| 📚 **Subject Management** | Add, edit, and manage subjects |
| 📅 **Academic Year Settings** | Configure academic terms and periods |
| 📊 **Report Generation** | Generate various academic and system reports |
| 💾 **Backup & Restore** | Automated system backup and restore functionality |
| 🎫 **Support System** | Manage support tickets and inquiries |
| ⚙️ **System Settings** | Configure system-wide settings |
| 🗄️ **Archived Records** | Manage archived students and teachers with auto-deletion cron jobs |

---

## 🛠️ Tech Stack

### Backend

<div align="center">

| Technology | Version | Purpose |
|------------|---------|---------|
| **PHP** | 7.4+ | Pure PHP (no frameworks) - File-based routing |
| **MySQL** | 5.7+ | Database management system |
| **Session Management** | - | PHP sessions for authentication |

</div>

### Frontend

<div align="center">

| Technology | Version | Purpose |
|------------|---------|---------|
| **HTML5** | - | Semantic markup |
| **CSS3** | - | Custom styling with separate CSS files per module |
| **JavaScript** | - | Vanilla JS and jQuery for interactivity |
| **Bootstrap** | 4 | Responsive UI framework |
| **Font Awesome** | 6.4.0 | Icon library |
| **SweetAlert2** | - | Modern alert dialogs |

</div>

### PHP Libraries (via Composer)

| Library | Version | Purpose |
|---------|---------|---------|
| **tecnickcom/tcpdf** | ^6.6 | PDF generation for reports and exports |
| **phpoffice/phpspreadsheet** | ^3.6 | Excel file handling for imports/exports |
| **phpmailer/phpmailer** | ^6.9 | Email functionality |
| **smalot/pdfparser** | ^2.11 | PDF parsing capabilities |

---

## 🚀 Getting Started

### 📋 Prerequisites

Before you begin, ensure you have the following installed:

- ✅ **PHP** 7.4 or higher
- ✅ **MySQL** 5.7 or higher (or MariaDB equivalent)
- ✅ **Web server** (Apache/Nginx)
- ✅ **Composer** (for dependency management)
- ✅ **PHP extensions**: `mysqli`, `mbstring`, `gd`, `zip`

### 🔧 Installation

#### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/camerino-school-system.git
cd camerino-school-system
```

#### Step 2: Install PHP Dependencies

```bash
composer install
```

#### Step 3: Configure Base URL

Edit `config/config.php` and update the `BASE_URL` constant:

```php
define('BASE_URL', '/your-installation-path/');
```

#### Step 4: Configure Database

1. Create a new MySQL database
2. Edit `db/dbConnector.php` and update database credentials:

```php
private $host = 'localhost';
private $username = 'your_username';
private $password = 'your_password';
private $database = 'your_database_name';
```

> 💡 **Tip:** For production, use environment variables instead of hardcoded credentials.

#### Step 5: Set Up File Permissions

```bash
# Make uploads directory writable
chmod -R 755 uploads/

# Make backups directory writable
chmod -R 755 backups/
```

#### Step 6: Configure Web Server

**For Apache:**
- Point document root to the project directory
- Ensure `mod_rewrite` is enabled (if using .htaccess)

**For Nginx:**
- Configure proper PHP-FPM settings
- Set up appropriate location blocks

#### Step 7: Set Up Cron Jobs (Optional)

For automated archive deletion:

```bash
0 0 * * * php /path/to/admin/cron/auto_delete_archived.php
```

---

## 📁 Project Structure

```
Camerino-Hub/
│
├── 📂 admin/                    # Administrator portal
│   ├── 📂 handlers/            # Request handlers for admin operations
│   ├── 📂 includes/             # Reusable admin components
│   ├── 📂 functions/            # Admin-specific functions
│   ├── 📂 cron/                 # Scheduled tasks
│   └── 📄 *.php                # Admin pages
│
├── 📂 Student/                  # Student portal
│   ├── 📂 handlers/            # Request handlers for student operations
│   ├── 📂 includes/            # Reusable student components
│   ├── 📂 css/                 # Student-specific stylesheets
│   ├── 📂 js/                  # Student-specific JavaScript
│   └── 📄 *.php                # Student pages
│
├── 📂 Teacher/                  # Teacher portal
│   ├── 📂 handlers/            # Request handlers for teacher operations
│   ├── 📂 includes/            # Reusable teacher components
│   ├── 📂 css/                 # Teacher-specific stylesheets
│   ├── 📂 js/                  # Teacher-specific JavaScript
│   └── 📄 *.php                # Teacher pages
│
├── 📂 config/                   # Configuration files
│   └── 📄 config.php           # Base URL and path configurations
│
├── 📂 db/                       # Database layer
│   └── 📄 dbConnector.php      # Database connection class
│
├── 📂 handlers/                 # Shared request handlers
├── 📂 images/                   # Image assets
├── 📂 uploads/                  # User-uploaded files
├── 📂 backups/                  # System backup files
├── 📂 vendor/                   # Composer dependencies
│
├── 📄 login.php                # Main login page
├── 📄 login_action.php         # Login authentication handler
├── 📄 forgot-password.php      # Password recovery page
├── 📄 composer.json            # PHP dependencies
└── 📄 README.md                # This file
```

---

## 🏗️ Architecture

This is a **pure PHP application** with no frameworks. The architecture follows a traditional file-based routing pattern:

### 🎯 Key Components

| Component | Description |
|-----------|-------------|
| **Entry Point** | `login.php` serves as the main entry point |
| **Routing** | File-based routing (each page is a separate PHP file) |
| **Authentication** | Session-based authentication with role checking |
| **Database Layer** | Custom database connector class with prepared statements |
| **Separation of Concerns** | Pages, handlers, includes, and configuration are organized separately |

### 📐 Architecture Pattern

```
┌─────────────────┐
│   login.php     │  ← Entry Point
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
┌───▼───┐ ┌──▼────┐ ┌───────┐
│Student│ │Teacher│ │ Admin │  ← Role-Based Portals
└───┬───┘ └───┬───┘ └───┬───┘
    │         │         │
    └────┬────┴────┬────┘
         │         │
    ┌────▼─────────▼────┐
    │  dbConnector.php  │  ← Database Layer
    └───────────────────┘
```

---

## 🔒 Security Features

<div align="center">

| Security Feature | Status | Description |
|-----------------|--------|-------------|
| 🔐 **Session Management** | ✅ | PHP sessions with proper session handling |
| 🔑 **Password Hashing** | ⚠️ | MD5 hashing (consider upgrading to bcrypt/argon2) |
| 🛡️ **SQL Injection Prevention** | ✅ | Prepared statements via `DbConnector::prepare()` |
| 🚫 **XSS Protection** | ✅ | Input sanitization and output escaping |
| 📁 **File Upload Security** | ✅ | File type validation and secure storage |
| 🎯 **Anti-Cheat System** | ✅ | Quiz anti-cheat protection for students |
| 👥 **Role-Based Access Control** | ✅ | Separate authentication for students, teachers, and admins |
| ✔️ **Input Validation** | ✅ | Server-side validation for all user inputs |
| 📝 **Error Handling** | ✅ | Error logging without exposing sensitive information |

</div>

---

## 🔄 Backup & Restore

The system includes comprehensive backup functionality:

- ✅ **Manual Backup Creation** - Via admin panel
- ⏰ **Scheduled Backups** - Automated via cron jobs
- 🔄 **Restore Functionality** - With backup history
- 📦 **Backup Storage** - Files stored in `backups/` directory

---

## 📱 Mobile Support

- 📱 **Responsive Design** - Bootstrap 4 responsive framework
- 📲 **Mobile-Friendly Interface** - Optimized for all user roles
- 🤖 **Android App** - Available for download (link in login page)

---

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. 🍴 **Fork** the repository
2. 🌿 **Create** your feature branch (`git checkout -b feature/AmazingFeature`)
3. 💾 **Commit** your changes (`git commit -m 'Add some AmazingFeature'`)
4. 📤 **Push** to the branch (`git push origin feature/AmazingFeature`)
5. 🔀 **Open** a Pull Request

---

## ✨ Future Enhancements

- [ ] 🔐 Upgrade password hashing from MD5 to bcrypt/argon2
- [ ] 🔌 Implement RESTful API structure
- [ ] 🧪 Add unit and integration tests
- [ ] 🛡️ Implement proper CSRF token protection
- [ ] 📚 Add API documentation
- [ ] 📱 Enhanced mobile application features
- [ ] 👨‍👩‍👧 Parent portal implementation
- [ ] 📖 Library management system
- [ ] 📊 Advanced analytics and reporting
- [ ] 🔔 Real-time notifications (WebSocket)
- [ ] 🌍 Multi-language support

---

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

## 👥 Contact

<div align="center">

### 🏫 Gov D.M. Camerino High School

**📍 Address:** Medicion 2, Imus City, Cavite 4103, Philippines

**📧 Email:** profcamerino@yahoo.com

**📞 Phone:** +(64) 456-5874, +(64) 456-5875

</div>

---

## 🏫 About

This system was developed to support the educational initiatives of **Gov D.M. Camerino High School**, in partnership with:

<div align="center">

| Partner | Description |
|---------|-------------|
| 🎓 **Department of Education (DepEd)** | National education department |
| 🇵🇭 **Bagong Pilipinas Initiative** | National development program |
| 🏙️ **City of Imus** | Local government unit |
| 🗺️ **Province of Cavite** | Provincial government |

</div>

---

<div align="center">

### Made with ❤️ for Gov D.M. Camerino High School

**Empowering education through digital innovation**

⭐ Star this repo if you find it helpful!

---

[⬆ Back to Top](#-gov-dm-camerino-school-management-system)

</div>
