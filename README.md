# Squared - QR Attendance System

A modern, fast, and secure QR-based attendance system designed for educational institutions. Track student attendance efficiently with QR code scanning technology.

## 🚀 Features

### Core Functionality
- **QR Code Attendance**: Generate and scan QR codes for quick attendance tracking
- **Real-time Scanning**: Instant attendance recording with mobile-friendly interface
- **Student Dashboard**: View personal attendance records and statistics
- **Admin Panel**: Comprehensive management system for administrators
- **Multi-device Support**: Works on smartphones, tablets, and desktop computers

### Advanced Features
- **Progressive Web App (PWA)**: Install as a native app on mobile devices
- **Event-based Attendance**: Special scanning for specific events and programs
- **Avatar Management**: Student profile picture uploads
- **Notification System**: Email and in-app notifications
- **Voting System**: Integrated polling and voting capabilities
- **Password Recovery**: Secure forgot password functionality
- **Anti-bot Protection**: Google reCAPTCHA integration

### Security Features
- **HTTP Security Headers**: HSTS, CSP, X-Frame-Options, and more
- **Directory Protection**: Prevents unauthorized file access
- **Input Validation**: Comprehensive form validation and sanitization
- **Session Management**: Secure user authentication and sessions
- **SQL Injection Protection**: Prepared statements and parameterized queries

## 🛠 Technology Stack

### Backend
- **PHP 8.x**: Server-side logic and API endpoints
- **MySQL**: Database management (squared.sql)
- **nginx**: Web server with security configurations

### Frontend
- **Bootstrap 5.3**: Responsive UI framework
- **Bootstrap Icons**: Icon library
- **Google Fonts**: Nunito font family
- **JavaScript ES6+**: Modern client-side functionality
- **Service Worker**: PWA offline capabilities

### Third-party Integrations
- **Google reCAPTCHA**: Bot protection
- **jsQR**: QR code scanning library
- **PHP QR Code**: QR code generation library

## 📁 Project Structure

```
squared/
├── index.html              # Landing page with PWA features
├── index.php               # Main application entry point
├── about.php               # About page
├── admin/                  # Admin panel directory
├── avatars/                # User profile pictures
├── css/                    # Stylesheets
├── js/                     # JavaScript files
├── images/                 # Static images and logos
├── php/                    # PHP utility classes
├── phpqrcode/              # QR code generation library
├── qr_images/              # Generated QR codes
├── uploads/                # File upload directory
├── manifest.json           # PWA manifest
├── squared.sql             # Database schema
└── .htaccess files         # Directory security configurations
```

## 🚀 Installation

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- nginx or Apache web server
- SSL certificate (for production)

### Setup Instructions

1. **Database Setup**
   ```sql
   Create database and import squared.sql
   ```

2. **Configuration**
   - Update database credentials in PHP files
   - Configure nginx/Apache settings
   - Set up SSL certificate

3. **File Permissions**
   ```bash
   chmod 755 avatars/ uploads/ qr_images/
   chmod 644 avatars/.htaccess uploads/.htaccess
   ```

4. **Web Server Configuration**
   - Add security headers (see nginx configuration)
   - Disable directory listings
   - Configure SSL/TLS

### Nginx Configuration
Add these security headers to your server block:
```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://www.google.com https://www.gstatic.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; img-src 'self' data: https:; font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com; connect-src 'self' https://cdn.jsdelivr.net; frame-src 'self' https://www.facebook.com https://www.google.com; frame-ancestors 'self';" always;
```

## 🔐 Security Considerations

### Implemented Security Measures
- **Content Security Policy**: Prevents XSS and code injection
- **HTTP Strict Transport Security**: Enforces HTTPS connections
- **Directory Protection**: Prevents unauthorized file access
- **Input Validation**: Comprehensive form validation
- **Rate Limiting**: Protection against brute force attacks
- **Secure Headers**: Multiple layers of HTTP security headers

### Recommended Security Practices
- Regular security audits and updates
- Monitor access logs for suspicious activity
- Keep all dependencies updated
- Use strong, unique passwords for admin accounts
- Regular database backups

## 📱 Mobile App Features

### Progressive Web App (PWA)
- **Offline Support**: Basic functionality without internet
- **App-like Experience**: Native app feel on mobile devices
- **Push Notifications**: Future capability for attendance alerts
- **Home Screen Installation**: Add to device home screen

### Mobile Optimizations
- **Responsive Design**: Works on all screen sizes
- **Touch-friendly Interface**: Optimized for touch interactions
- **Camera Integration**: Built-in QR code scanning
- **In-app Browser Detection**: Prompts Chrome for better experience

## 🌐 Deployment

### Production Environment
- **Domain**: squared-qr.duckdns.org
- **SSL**: Let's Encrypt certificate
- **CDN**: jsDelivr for Bootstrap and other libraries
- **Monitoring**: Security headers and CSP monitoring

### Environment Variables
Configure these settings for production:
- Database connection details
- Google reCAPTCHA keys
- Email settings for notifications
- File upload limits and restrictions

## 🤝 Contributing

### Development Guidelines
- Follow PHP coding standards
- Use semantic HTML5 markup
- Implement proper error handling
- Test mobile responsiveness
- Validate security implementations

### Code Structure
- Modular PHP classes in `/php/` directory
- Separate CSS files for different components
- JavaScript modules for specific functionality
- Database abstraction layer for security

## 📊 Features Overview

### User Roles
- **Students**: View attendance, scan QR codes, manage profiles
- **Administrators**: Full system management and reporting
- **Teachers**: Class management and attendance monitoring

### Attendance Methods
- **QR Code Scanning**: Primary method using mobile cameras
- **Manual Entry**: Backup method for special circumstances
- **Bulk Import**: Excel/CSV file uploads for mass data
- **Event Scanning**: Special QR codes for specific events

### Reporting & Analytics
- **Individual Records**: Personal attendance history
- **Class Statistics**: Group attendance analytics
- **Export Functions**: Download reports in various formats
- **Real-time Monitoring**: Live attendance tracking

## 🔧 Maintenance

### Regular Tasks
- Database optimization and cleanup
- Log file rotation and monitoring
- Security header updates
- SSL certificate renewal
- Backup verification

### Troubleshooting
- Check nginx error logs for issues
- Verify database connections
- Test file upload permissions
- Monitor CSP violations in browser console
- Validate security headers regularly

## 📞 Support

For technical support or questions about the Squared QR Attendance System:
- Check the admin panel for system status
- Review error logs for troubleshooting
- Verify all security configurations are properly set

---

**Squared** - Modernizing attendance tracking with QR technology.
