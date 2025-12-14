-- GOSHOP Loyalty Platform - 


CREATE DATABASE IF NOT EXISTS goshop_loyalty
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE goshop_loyalty;


CREATE TABLE customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(50) NULL,
  city VARCHAR(120) NULL,
  address TEXT NULL,
  avatar_url VARCHAR(255) NULL,
  tier VARCHAR(30) NOT NULL DEFAULT 'SILVER',
  points_balance INT NOT NULL DEFAULT 0,
  total_rewards_redeemed INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  category VARCHAR(100) NULL,
  price INT NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  image_url VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


CREATE TABLE rewards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  points_required INT NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  image_url VARCHAR(255) NULL,
  category VARCHAR(100) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


CREATE TABLE point_transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  type ENUM('EARN','REDEEM','ADJUST') NOT NULL,
  description VARCHAR(255) NOT NULL,
  points INT NOT NULL,
  source VARCHAR(50) NOT NULL DEFAULT 'ONLINE',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ptx_customer
    FOREIGN KEY (customer_id) REFERENCES customers(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  total_amount INT NOT NULL,
  points_earned INT NOT NULL DEFAULT 0,
  status ENUM('PAID','PENDING','CANCELLED') NOT NULL DEFAULT 'PAID',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_customer
    FOREIGN KEY (customer_id) REFERENCES customers(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  qty INT NOT NULL,
  price INT NOT NULL,
  subtotal INT NOT NULL,
  CONSTRAINT fk_orderitems_order
    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_orderitems_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB;


CREATE TABLE redemptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  reward_id INT NOT NULL,
  points_used INT NOT NULL,
  status ENUM('APPROVED','PENDING','REJECTED') NOT NULL DEFAULT 'APPROVED',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_red_customer
    FOREIGN KEY (customer_id) REFERENCES customers(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_red_reward
    FOREIGN KEY (reward_id) REFERENCES rewards(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB;


INSERT INTO customers (name,email,phone,city,address,avatar_url,tier,points_balance,total_rewards_redeemed)
VALUES
('Budi Santoso','budi@example.com','0812-3456-7890','Jakarta','Jl. Contoh No. 123, Jakarta',NULL,'SILVER',130,0);

INSERT INTO products (name,category,price,stock,image_url,is_active) VALUES
('GO SHOP Tote Bag','Fashion',50000,20,'assets/tote-bag.jpg',1),
('Earphone GO Sound','Elektronik',150000,12,'assets/earphone.jpg',1),
('Snack Pack GO Crunch','Makanan & Minuman',25000,4,'assets/snack-pack.jpg',1),
('GO SHOP Hoodie','Merchandise',250000,0,'assets/hoodie.jpg',1);

INSERT INTO rewards (name,points_required,stock,image_url,category,is_active) VALUES
('Voucher GO SHOP 50K',80,15,'assets/voucher.jpg','Voucher Belanja',1),
('Voucher Makan 100K',150,5,'assets/voucher.jpg','Makanan & Minuman',1),
('GO SHOP Tote Bag',60,20,'assets/tote-bag.jpg','Merchandise',1),
('GO SHOP T-Shirt',120,0,'assets/t-shirt.jpg','Merchandise',1);

-- point history
INSERT INTO point_transactions (customer_id,type,description,points,source) VALUES
(1,'EARN','Belanja di GO SHOP Mall',150,'OFFLINE'),
(1,'REDEEM','Penukaran Voucher Diskon 20%',-100,'ONLINE'),
(1,'EARN','Belanja di GO SHOP Online',200,'ONLINE');


