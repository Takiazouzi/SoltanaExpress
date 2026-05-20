# Database Schema Documentation

## Overview
This document describes the relational schema for the SoltanaExpress restaurant web application. The schema supports user authentication, menu management, order processing, reservations, and administrative statistics.

---

## Tables

### `users`
Stores customer and administrator accounts.
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique user identifier |
| `name` | VARCHAR(100) | NOT NULL | Full name of the user |
| `email` | VARCHAR(150) | UNIQUE, NOT NULL | Login & contact email |
| `password` | VARCHAR(255) | NOT NULL | Bcrypt-hashed password |
| `role` | ENUM('user','admin') | NOT NULL, DEFAULT 'user' | Access role |
| `avatar` | VARCHAR(255) | NULL | URL/path to profile image |
| `created_at` | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Account creation timestamp |

### `menu_items`
Catalog of dishes available for ordering.
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique menu item ID |
| `name` | VARCHAR(100) | NOT NULL | Dish name |
| `description` | TEXT | NULL | Detailed description & ingredients |
| `price` | DECIMAL(10,2) | NOT NULL | Selling price |
| `category` | VARCHAR(50) | NOT NULL | e.g., Starters, Mains, Desserts |
| `image_path` | VARCHAR(255) | NULL | Path to uploaded image |
| `available` | TINYINT(1) | DEFAULT 1 | Visibility/stock flag (1=active, 0=hidden) |
| `created_at` | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Entry creation timestamp |

### `orders`
Customer purchase records.
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique order ID |
| `user_id` | INT | FOREIGN KEY → users(id), NOT NULL | Customer who placed the order |
| `status` | ENUM('pending','confirmed','preparing','ready','delivered','cancelled') | DEFAULT 'pending' | Order lifecycle stage |
| `total` | DECIMAL(10,2) | NOT NULL | Calculated total at checkout |
| `notes` | TEXT | NULL | Customer instructions or allergies |
| `created_at` | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Order placement timestamp |

### `order_items`
Line items linking orders to specific dishes.
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique line item ID |
| `order_id` | INT | FOREIGN KEY → orders(id), NOT NULL | Parent order reference |
| `menu_item_id` | INT | FOREIGN KEY → menu_items(id), NOT NULL | Dish reference |
| `quantity` | INT | NOT NULL, CHECK (quantity > 0) | Number of units ordered |
| `unit_price` | DECIMAL(10,2) | NOT NULL | Price at time of order (snapshotted) |

### `reservations`
Table booking records.
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique reservation ID |
| `user_id` | INT | FOREIGN KEY → users(id), NOT NULL | Customer making reservation |
| `date` | DATE | NOT NULL | Booking date |
| `time` | TIME | NOT NULL | Booking time |
| `guests` | INT | NOT NULL, CHECK (guests > 0) | Party size |
| `status` | ENUM('pending','confirmed','cancelled') | DEFAULT 'pending' | Reservation status |
| `special_requests` | TEXT | NULL | Dietary needs or table preferences |
| `created_at` | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Booking creation timestamp |

---

## Relationships

| Relationship | Type | Description |
|--------------|------|-------------|
| `User` → `Order` | 1:N | A user can place many orders |
| `User` → `Reservation` | 1:N | A user can make many reservations |
| `Order` → `OrderItem` | 1:N (Composition) | Each order contains one or more line items |
| `OrderItem` → `MenuItem` | N:1 | Each line item references exactly one menu item |

---

## Indexes & Constraints
- `users.email`: Unique index for login & duplicate prevention
- `orders.user_id`, `orders.status`: Composite index for admin filtering
- `reservations.user_id`, `reservations.date`: Composite index for calendar views & user history
- `menu_items.category`: Index for category-based filtering
- `order_items.order_id`, `order_items.menu_item_id`: Foreign key indexes for JOIN performance
- Soft delete pattern: `available = 0` hides menu items without removing historical order data

---

## Notes
- All monetary values use `DECIMAL(10,2)` to avoid floating-point precision errors
- `unit_price` in `order_items` is snapshotted at checkout to preserve historical pricing even if `menu_items.price` changes later
- Passwords are hashed using `PASSWORD_BCRYPT` (cost 12)
- Sessions are tied to `users.id` and `users.role` for RBAC enforcement
