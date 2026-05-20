# SoltanaExpress 🍽️

A modern, full-featured restaurant management platform built with **PHP**, **MariaDB**, and **Vanilla JavaScript**.

SoltanaExpress provides a complete restaurant ecosystem including:

- 🍔 Online food ordering
- 📅 Reservation management
- 👤 Customer accounts
- 🛠️ Admin dashboard
- 📊 Analytics & activity tracking
- 🔐 Secure authentication system

---

![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php)
![MariaDB](https://img.shields.io/badge/MariaDB-11.8-003545?style=flat&logo=mariadb)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=flat&logo=javascript)
![License](https://img.shields.io/badge/license-MIT-blue.svg)

---

# 📖 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Installation](#-installation)
- [Environment Variables](#-environment-variables)
- [Database Setup](#-database-setup)
- [Running the Application](#-running-the-application)
- [Default Admin Credentials](#-default-admin-credentials)
- [API Documentation](#-api-documentation)
- [Development Commands](#-development-commands)
- [Security Practices](#-security-practices)
- [Deployment Notes](#-deployment-notes)
- [Nginx Configuration](#-nginx-configuration)
- [License](#-license)

---

# 🍴 Overview

SoltanaExpress is a restaurant management and online ordering platform designed for both customers and administrators.

The application combines a clean customer-facing experience with a powerful admin dashboard for managing:

- Menu items
- Orders
- Reservations
- User activity
- Restaurant analytics

The platform is fully responsive and optimized for desktop, tablet, and mobile devices.

---

# ✨ Features

## 👥 Customer Features

### 🍔 Menu Browsing
- Browse dishes by category
- Search menu items instantly
- Responsive menu interface
- Dynamic cart system

### 🛒 Ordering System
- Add/remove cart items
- Checkout workflow
- Order history tracking
- Real-time order summary

### 📅 Reservations
- Book restaurant tables
- Select date & time
- Specify guest count
- Add special requests

### 👤 User Profiles
- Account management
- Reservation tracking
- Order history
- Session-based authentication

---

## 🛠️ Admin Dashboard

### 📦 Menu Management
- Create menu items
- Update dish details
- Delete items
- Upload images (JPG/PNG/WebP)

### 📋 Order Management
- View all orders
- Update order statuses
- Expandable order details
- Track order lifecycle

### 🪑 Reservation Management
- Confirm reservations
- Cancel reservations
- Filter by date
- View customer details

### 📊 Analytics
- Revenue metrics
- Order statistics
- Reservation analytics
- Chart.js visualizations

### 🕒 Activity Feed
- Real-time admin activity
- User registration logs
- Order tracking events
- Reservation activity

---

# 🚀 Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.4 |
| Database | MariaDB 11.8 |
| Frontend | HTML5, CSS3, Vanilla JS |
| Charts | Chart.js 4.x |
| Icons | Tabler Icons |
| Authentication | PHP Sessions |
| Database Access | PDO |
| Development Server | PHP Built-in Server |
| Version Control | Git |

---

# 📁 Project Structure

\`\`\`text
SoltanaExpress/
│
├── config/
│   └── env.php
│
├── public/
│   ├── css/
│   ├── js/
│   ├── api/
│   ├── admin/
│   ├── uploads/
│   ├── index.php
│   ├── menu.php
│   ├── reservation.php
│   ├── profile.php
│   ├── login.php
│   ├── register.php
│   └── logout.php
│
├── src/
│   ├── models/
│   └── controllers/
│
├── uploads/
├── .env
├── .env.example
├── .gitignore
├── database.sql
└── README.md
\`\`\`

---

# ⚙️ Installation

## 1️⃣ Clone Repository

\`\`\`bash
git clone https://github.com/yourusername/soltana-express.git
cd soltana-express
\`\`\`

---

## 2️⃣ Install Required Packages

### Debian / Ubuntu / Kali

\`\`\`bash
sudo apt update

sudo apt install -y \
php8.4 \
php8.4-cli \
php8.4-mysql \
php8.4-mbstring \
php8.4-xml \
php8.4-curl \
php8.4-zip \
php8.4-intl \
php8.4-fileinfo \
mariadb-server \
git
\`\`\`

---

## 3️⃣ Start MariaDB

\`\`\`bash
sudo systemctl enable mariadb
sudo systemctl start mariadb
\`\`\`

---

## 4️⃣ Create Environment File

\`\`\`bash
cp .env.example .env
nano .env
\`\`\`

---

# 🔧 Environment Variables

\`\`\`env
APP_ENV=development
APP_URL=http://localhost:8110

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=restaurant

DB_USER=restaurant_app
DB_PASS=your_secure_password

MAX_UPLOAD_SIZE=2097152
ALLOWED_IMAGE_EXTENSIONS=jpg,jpeg,png,webp
\`\`\`

---

# 🗄️ Database Setup

## Create Database & User

\`\`\`bash
sudo mysql <<'SQL'
CREATE DATABASE IF NOT EXISTS restaurant
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'restaurant_app'@'localhost'
IDENTIFIED BY 'your_secure_password';

GRANT ALL PRIVILEGES ON restaurant.* TO 'restaurant_app'@'localhost';

FLUSH PRIVILEGES;
SQL
\`\`\`

---

## Import Database Schema

\`\`\`bash
mysql -u restaurant_app -p restaurant < database.sql
\`\`\`

---

# ▶️ Running the Application

## Start PHP Development Server

\`\`\`bash
php -S localhost:8110 -t public
\`\`\`

---

## Open in Browser

\`\`\`text
http://localhost:8110
\`\`\`

---

# 🔑 Default Admin Credentials

| Email | Password | Role |
|------|------|------|
| admin@savoria.com | admin123 | Administrator |

> ⚠️ Change the default administrator password immediately in production.

---

# 🌐 API Documentation

## Public API

| Endpoint | Method | Description | Auth |
|----------|--------|-------------|------|
| /api/auth.php | POST | Login / Register / Logout | No |
| /api/menu.php | GET | Fetch menu items | No |
| /api/order.php | POST | Create orders | User |
| /api/reservation.php | GET/POST | Reservations | User |

---

## Admin API

| Endpoint | Method | Description | Auth |
|----------|--------|-------------|------|
| /admin/Activity.php | GET | Activity feed | Admin |
| /admin/Order.php | GET/POST | Manage orders | Admin |
| /admin/Reservation.php | GET/POST | Manage reservations | Admin |
| /admin/menu-items.php | GET/POST | Menu CRUD | Admin |

---

# 🧪 Development Commands

## Start Development Server

\`\`\`bash
php -S localhost:8110 -t public
\`\`\`

---

## Check PHP Syntax

\`\`\`bash
find . -type f -name "*.php" -exec php -l {} \;
\`\`\`

---

## Test Database Connection

\`\`\`bash
php -r '
require "config/env.php";

\$pdo = new PDO(
    "mysql:host=" . getenv("DB_HOST") .
    ";dbname=" . getenv("DB_NAME"),
    getenv("DB_USER"),
    getenv("DB_PASS")
);

echo "Database connection successful\n";
'
\`\`\`

---

## Run on Another Port

\`\`\`bash
php -S localhost:8080 -t public
\`\`\`

---

# 🔐 Security Practices

- Password hashing using `password_hash()`
- PDO prepared statements
- Session regeneration on login
- Environment-based configuration
- File upload validation
- Secure admin route protection
- Role-based access control
- Server-side validation

---

# 🚀 Deployment Notes

## Production Checklist

- Set `APP_ENV=production`
- Use Nginx or Apache
- Enable HTTPS
- Configure PHP-FPM
- Restrict upload permissions
- Configure backups
- Monitor logs
- Rotate logs regularly
- Change default credentials

---

# 🌍 Nginx Configuration

\`\`\`nginx
server {
    listen 80;
    server_name yourdomain.com;

    root /var/www/soltana-express/public;
    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \\.php\$ {
        include snippets/fastcgi-php.conf;

        fastcgi_pass unix:/run/php/php8.4-fpm.sock;

        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;

        include fastcgi_params;
    }

    location ~ /\\. {
        deny all;
    }

    location ~* \\.(jpg|jpeg|png|gif|ico|css|js|webp)\$ {
        expires 30d;
    }
}
\`\`\`

---

# 📄 License

This project is licensed under the MIT License.

---

# ⭐ Acknowledgments

- PHP Community
- MariaDB
- Chart.js
- Tabler Icons
- Open Source Contributors

