# Gov D.M. Camerino School Management System

A comprehensive web-based school management system for Gov D.M. Camerino, designed to streamline educational processes and enhance communication between students, teachers, and administrators.

## 🎯 Features

### For Students
- **Dashboard**: Access to personal academic information
- **Course Management**: View enrolled classes and materials
- **Profile Management**: Update personal information
- **Grades Viewing**: Check academic performance
- **Calendar**: Track events and assignments
- **Announcements**: Stay updated with school news

### For Teachers
- **Class Management**: Manage student rosters
- **Grade Management**: Input and update grades
- **Attendance Tracking**: Record student attendance
- **Assignment Creation**: Create and manage assignments
- **Announcement System**: Post class announcements

### For Administrators
- **User Management**: Manage student and teacher accounts
- **Academic Year Settings**: Configure academic terms
- **Report Generation**: Generate various academic reports
- **System Configuration**: Manage system settings

## 🛠️ Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 4
- **Backend**: PHP
- **Database**: MySQL
- **Additional Libraries**: 
  - Font Awesome
  - jQuery
  - Popper.js

## 🚀 Getting Started

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Installation

1. Clone the repository
bash
git clone https://github.com/yourusername/camerino-school-system.git


2. Import the database
bash
mysql -u username -p database_name < database.sql


3. Configure database connection
- Navigate to `db/dbConnector.php`
- Update database credentials

4. Set up your web server
- Point your web server to the project directory
- Ensure proper permissions are set

## 📁 Project Structure
├── admin/ # Administrator interface
├── db/ # Database configuration
├── images/ # Image assets
├── includes/ # Reusable components
├── student/ # Student interface
├── teacher/ # Teacher interface
└── README.md

## 🔒 Security Features

- Session management
- Password hashing
- SQL injection prevention
- XSS protection
- CSRF protection

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## ✨ Future Enhancements

- [ ] Mobile application integration
- [ ] Online examination system
- [ ] Parent portal
- [ ] Library management system
- [ ] Advanced reporting system

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Contact

For any queries or support, please contact:
- Email: profcamerino@yahoo.com
- Phone: +(64) 456-5874

---
Made with ❤️ for Gov D.M. Camerino School
