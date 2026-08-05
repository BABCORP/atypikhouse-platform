CREATE DATABASE IF NOT EXISTS atypikhouse CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE atypikhouse;

DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS blog_posts;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS property_change_requests;
DROP TABLE IF EXISTS property_favorites;
DROP TABLE IF EXISTS property_availabilities;
DROP TABLE IF EXISTS property_amenities;
DROP TABLE IF EXISTS property_images;
DROP TABLE IF EXISTS properties;
DROP TABLE IF EXISTS owner_profiles;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  phone VARCHAR(40) NULL,
  role ENUM('tenant', 'owner', 'admin') NOT NULL DEFAULT 'tenant',
  status ENUM('active', 'pending', 'rejected', 'suspended') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  INDEX idx_users_email (email),
  INDEX idx_users_role (role),
  INDEX idx_users_status (status)
) ENGINE=InnoDB;

CREATE TABLE owner_profiles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  company_name VARCHAR(190) NULL,
  address VARCHAR(255) NULL,
  city VARCHAR(120) NULL,
  postal_code VARCHAR(20) NULL,
  country VARCHAR(80) NOT NULL DEFAULT 'France',
  description TEXT NULL,
  verification_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_owner_profiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE properties (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  owner_id INT UNSIGNED NOT NULL,
  title VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NOT NULL UNIQUE,
  type ENUM('treehouse', 'yurt', 'floating_cabin', 'tiny_house', 'dome', 'other') NOT NULL,
  short_description VARCHAR(255) NOT NULL,
  long_description TEXT NOT NULL,
  address VARCHAR(255) NULL,
  city VARCHAR(120) NOT NULL,
  postal_code VARCHAR(20) NULL,
  region VARCHAR(120) NOT NULL,
  country VARCHAR(80) NOT NULL DEFAULT 'France',
  latitude DECIMAL(10, 7) NULL,
  longitude DECIMAL(10, 7) NULL,
  capacity TINYINT UNSIGNED NOT NULL,
  bedrooms TINYINT UNSIGNED NOT NULL DEFAULT 1,
  beds TINYINT UNSIGNED NOT NULL DEFAULT 1,
  bathrooms TINYINT UNSIGNED NOT NULL DEFAULT 1,
  price_per_night DECIMAL(10,2) NOT NULL,
  cleaning_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
  eco_score TINYINT UNSIGNED NOT NULL DEFAULT 3,
  status ENUM('draft', 'pending', 'published', 'rejected', 'archived', 'paused', 'deleted') NOT NULL DEFAULT 'draft',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_properties_owner FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_properties_owner_id (owner_id),
  INDEX idx_properties_slug (slug),
  INDEX idx_properties_city (city),
  INDEX idx_properties_region (region),
  INDEX idx_properties_type (type),
  INDEX idx_properties_status (status)
) ENGINE=InnoDB;

CREATE TABLE property_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  property_id INT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  alt_text VARCHAR(255) NOT NULL,
  is_main BOOLEAN NOT NULL DEFAULT FALSE,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_property_images_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
  INDEX idx_property_images_main_lookup (property_id, is_main, id)
) ENGINE=InnoDB;

CREATE TABLE property_amenities (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  property_id INT UNSIGNED NOT NULL,
  amenity_name VARCHAR(120) NOT NULL,
  CONSTRAINT fk_property_amenities_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
  INDEX idx_property_amenities_lookup (property_id, amenity_name)
) ENGINE=InnoDB;

CREATE TABLE property_availabilities (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  property_id INT UNSIGNED NOT NULL,
  date DATE NOT NULL,
  is_available BOOLEAN NOT NULL DEFAULT TRUE,
  price_override DECIMAL(10,2) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  UNIQUE KEY uniq_property_date (property_id, date),
  CONSTRAINT fk_property_availabilities_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE property_favorites (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  property_id INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL,
  UNIQUE KEY uniq_favorite_user_property (user_id, property_id),
  INDEX idx_property_favorites_user_id (user_id),
  INDEX idx_property_favorites_property_id (property_id),
  CONSTRAINT fk_property_favorites_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_property_favorites_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE property_change_requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  property_id INT UNSIGNED NOT NULL,
  owner_id INT UNSIGNED NOT NULL,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  proposed_data JSON NOT NULL,
  rejection_reason VARCHAR(500) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  reviewed_at DATETIME NULL,
  reviewed_by INT UNSIGNED NULL,
  CONSTRAINT fk_property_change_requests_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
  CONSTRAINT fk_property_change_requests_owner FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_property_change_requests_reviewer FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_property_change_requests_property_status (property_id, status),
  INDEX idx_property_change_requests_status (status)
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  property_id INT UNSIGNED NOT NULL,
  tenant_id INT UNSIGNED NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  nights INT UNSIGNED NOT NULL,
  guests_count TINYINT UNSIGNED NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  cleaning_fee DECIMAL(10,2) NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  status ENUM('pending_admin', 'pending_payment', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending_payment',
  payment_status ENUM('not_paid', 'test_paid', 'test_failed', 'refunded') NOT NULL DEFAULT 'not_paid',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_bookings_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
  CONSTRAINT fk_bookings_tenant FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_bookings_property_id (property_id),
  INDEX idx_bookings_tenant_id (tenant_id),
  INDEX idx_bookings_status (status),
  INDEX idx_bookings_payment_status (payment_status),
  INDEX idx_bookings_start_date (start_date),
  INDEX idx_bookings_end_date (end_date),
  INDEX idx_bookings_created_at (created_at),
  INDEX idx_bookings_availability_lookup (property_id, status, start_date, end_date),
  INDEX idx_bookings_tenant_order (tenant_id, start_date)
) ENGINE=InnoDB;

CREATE TABLE payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id INT UNSIGNED NOT NULL,
  provider VARCHAR(80) NOT NULL,
  test_transaction_id VARCHAR(120) NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  status ENUM('test_pending', 'test_success', 'test_failed', 'test_refunded') NOT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_payments_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reviews (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id INT UNSIGNED NOT NULL UNIQUE,
  property_id INT UNSIGNED NOT NULL,
  tenant_id INT UNSIGNED NOT NULL,
  rating TINYINT UNSIGNED NOT NULL,
  comment TEXT NOT NULL,
  status ENUM('pending', 'published', 'rejected') NOT NULL DEFAULT 'published',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_reviews_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  CONSTRAINT fk_reviews_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
  CONSTRAINT fk_reviews_tenant FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_reviews_property_id (property_id),
  INDEX idx_reviews_property_status (property_id, status),
  INDEX idx_reviews_status_created (status, created_at),
  CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;

CREATE TABLE blog_posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NOT NULL UNIQUE,
  excerpt VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  featured_image VARCHAR(255) NULL,
  status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
  author_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_blog_posts_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  email VARCHAR(190) NOT NULL,
  subject VARCHAR(190) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('new', 'read', 'processed', 'archived') NOT NULL DEFAULT 'new',
  created_at DATETIME NOT NULL,
  INDEX idx_contact_messages_status (status),
  INDEX idx_contact_messages_created_at (created_at)
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  action VARCHAR(120) NOT NULL,
  entity_type VARCHAR(120) NOT NULL,
  entity_id INT UNSIGNED NULL,
  ip_address VARCHAR(45) NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_audit_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_audit_logs_created_at (created_at),
  INDEX idx_audit_logs_action (action)
) ENGINE=InnoDB;

CREATE TABLE password_resets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  token VARCHAR(190) NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
