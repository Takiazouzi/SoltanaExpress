-- ============================================================================
-- Restaurant App Database Schema
-- ============================================================================

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  avatar VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS menu_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(8,2) NOT NULL,
  category VARCHAR(80) NOT NULL,
  image_path VARCHAR(255) DEFAULT NULL,
  available TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  status ENUM('pending','confirmed','preparing','ready','delivered','cancelled') DEFAULT 'pending',
  total DECIMAL(10,2) NOT NULL,
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  menu_item_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  unit_price DECIMAL(8,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  date DATE NOT NULL,
  time TIME NOT NULL,
  guests INT NOT NULL DEFAULT 2,
  status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  special_requests TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================================
-- Seed Data: 8 Realistic Menu Items
-- ============================================================================
INSERT INTO menu_items (name, description, price, category, available) VALUES
('Truffle Arancini', 'Crispy risotto balls infused with black truffle, served with garlic aioli.', 12.50, 'Starters', 1),
('Burrata & Heirloom Tomatoes', 'Fresh burrata cheese with vine-ripened tomatoes, basil oil, and balsamic glaze.', 14.00, 'Starters', 1),
('Charred Octopus', 'Tender octopus with smoked paprika, lemon potatoes, and olive tapenade.', 16.50, 'Starters', 1),
('Pan-Seared Salmon', 'Wild-caught salmon with asparagus, lemon beurre blanc, and crushed potatoes.', 28.00, 'Mains', 1),
('Wild Mushroom Risotto', 'Arborio rice slowly cooked with porcini, truffle oil, and aged parmesan.', 24.00, 'Mains', 1),
('Herb-Crusted Lamb Rack', 'New Zealand lamb with rosemary crust, roasted root vegetables, and red wine jus.', 34.00, 'Mains', 1),
('Dark Chocolate Fondant', 'Warm chocolate cake with a molten center, vanilla bean ice cream, and raspberry coulis.', 11.00, 'Desserts', 1),
('Lemon Basil Tart', 'Zesty lemon curd in a buttery shortcrust shell, topped with candied basil leaves.', 10.50, 'Desserts', 1);
