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

```text
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
