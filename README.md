cat > README.md << 'EOF'
# SoltanaExpress 🍽️

A modern, full-featured restaurant management platform built with **PHP**, **MariaDB**, and **Vanilla JavaScript**.  
It provides a seamless customer ordering experience alongside a powerful admin dashboard for managing menu items, orders, reservations, and analytics.

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
- [Installation & Setup](#-installation--setup)
- [Environment Variables](#-environment-variables)
- [Database Setup](#-database-setup)
- [Running the Application](#-running-the-application)
- [Default Admin Credentials](#-default-admin-credentials)
- [API Documentation](#-api-documentation)
- [Development Commands](#-development-commands)
- [Security Practices](#-security-practices)
- [Deployment Notes](#-deployment-notes)
- [Nginx Configuration](#-nginx-configuration)
- [Contributing](#-contributing)
- [License](#-license)

---

# 🍴 Overview

SoltanaExpress is a restaurant management and online ordering platform designed for both customers and administrators.

The platform includes:

- Customer-facing menu browsing and ordering
- Reservation management system
- Secure authentication system
- Admin dashboard with analytics
- Real-time activity monitoring
- Responsive UI for desktop and mobile devices

---

# ✨ Features

## 👥 Customer Features

### 🍔 Browse Menu
- Filter dishes by category
- Search menu items instantly
- View descriptions and pricing
- Responsive menu interface

### 🛒 Order System
- Add/remove items from cart
- Dynamic order summary
- Checkout workflow
- Order history tracking

### 📅 Reservations
- Create reservations
- Select date and time
- Guest count support
- Special request field

### 👤 User Profile
- Manage account information
- View previous orders
- Track reservations
- Update settings

### 📱 Responsive Design
- Mobile-first layout
- Tablet support
- Desktop optimized UI

---

## 🛠️ Admin Dashboard

### 📦 Menu Management
- Create menu items
- Edit item details
- Delete menu items
- Upload dish images
- Support for JPG, PNG, WebP

### 📋 Order Management
- View all orders
- Update order status
- Track order lifecycle
- Expandable order details

### 🪑 Reservation Management
- Confirm reservations
- Cancel reservations
- Filter reservations
- View customer details

### 📊 Analytics Dashboard
- Revenue statistics
- Order analytics
- Reservation metrics
- Chart.js visualizations

### 🕒 Activity Feed
- Live admin activity timeline
- User registrations
- Order tracking
- Reservation events

### 🔐 Role-Based Access
- Admin-only routes
- Session protection
- Authentication guards

---

# 🚀 Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.4 |
| Database | MariaDB 11.8 |
| Frontend | HTML5, CSS3, Vanilla JS |
| Charts | Chart.js 4.x |
| Icons | Tabler Icons |
| Auth | PHP Sessions |
| Database Access | PDO |
| Server | PHP Built-in Server |
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

# ⚙️ Installation & Setup

## 1️⃣ Prerequisites

- PHP 8.4+
- MariaDB 11.8+ or MySQL 8+
- PDO Extension
- MBString Extension
- Fileinfo Extension

---

## 2️⃣ Clone Repository

\`\`\`bash
git clone https://github.com/yourusername/soltana-express.git
cd soltana-express
\`\`\`

---

## 3️⃣ Create Environment File

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

## Start MariaDB

\`\`\`bash
sudo systemctl start mariadb
sudo systemctl enable mariadb
\`\`\`

---

## Create Database & User

\`\`\`bash
sudo mysql << 'SQL'
CREATE DATABASE IF NOT EXISTS restaurant
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'restaurant_app'@'127.0.0.1'
IDENTIFIED BY 'your_secure_password';

CREATE USER IF NOT EXISTS 'restaurant_app'@'localhost'
IDENTIFIED BY 'your_secure_password';

GRANT ALL PRIVILEGES ON restaurant.* TO 'restaurant_app'@'127.0.0.1';
GRANT ALL PRIVILEGES ON restaurant.* TO 'restaurant_app'@'localhost';

FLUSH PRIVILEGES;
SQL
\`\`\`

---

## Import Schema

\`\`\`bash
mysql -u restaurant_app -p -h 127.0.0.1 restaurant < database.sql
\`\`\`

---

# ▶️ Running the Application

\`\`\`bash
php -t public -S localhost:8110
\`\`\`

Open in browser:

\`\`\`text
http://localhost:8110
\`\`\`

---

# 🔑 Default Admin Credentials

| Email | Password | Role |
|------|------|------|
| admin@savoria.com | admin123 | Administrator |

> Change the default admin password immediately in production.

---

# 🌐 API Documentation

## Public API

| Endpoint | Method | Description | Auth |
|----------|--------|-------------|------|
| \`/api/auth.php\` | POST | Login / Register / Logout | No |
| \`/api/menu.php\` | GET | Get menu items | No |
| \`/api/order.php\` | POST | Place order | User |
| \`/api/reservation.php\` | GET/POST | Reservations | User |

---

## Admin API

| Endpoint | Method | Description | Auth |
|----------|--------|-------------|------|
| \`/admin/Activity.php\` | GET | Activity feed | Admin |
| \`/admin/Order.php\` | GET/POST | Orders management | Admin |
| \`/admin/Reservation.php\` | GET/POST | Reservations management | Admin |
| \`/admin/menu-items.php\` | GET/POST | Menu CRUD | Admin |

---

# 🧪 Development Commands

## Start Development Server

\`\`\`bash
php -t public -S localhost:8110
\`\`\`

---

## Check PHP Syntax

\`\`\`bash
find . -name "*.php" -exec php -l {} \\;
\`\`\`

---

## Test Database Connection

\`\`\`bash
php -r "require 'config/env.php'; \\$pdo = new PDO('mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASS')); echo 'DB OK';"
\`\`\`

---

## Run on Different Port

\`\`\`bash
php -t public -S localhost:8080
\`\`\`

---

# 🔐 Security Practices

- Password hashing using \`password_hash()\`
- PDO prepared statements
- Session regeneration on login
- Secure session handling
- Environment-based configuration
- Server-side validation
- File upload restrictions
- Role-based access control

---

# 🚀 Deployment Notes

## Production Checklist

- Set \`APP_ENV=production\`
- Use Apache or Nginx
- Enable HTTPS
- Configure PHP-FPM
- Secure file permissions
- Set upload limits
- Enable backups
- Rotate logs
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
        try_files \\$uri \\$uri/ /index.php?\\$query_string;
    }

    location ~ \\.php\\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;

        fastcgi_param SCRIPT_FILENAME \\$document_root\\$fastcgi_script_name;

        include fastcgi_params;
    }

    location ~ /\\. {
        deny all;
    }

    location ~* \\.(jpg|jpeg|png|gif|ico|css|js)\\$ {
        expires 30d;
    }
}
\`\`\`

---
EOF
