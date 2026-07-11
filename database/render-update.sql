-- Mise à jour non destructive pour une base MySQL déjà existante avant déploiement Render.
-- Pour une nouvelle base vide, importer plutôt database/schema.sql puis database/seed.sql.
-- Vérifier le nom de base avec `USE votre_base;` si votre client SQL ne le sélectionne pas déjà.

ALTER TABLE users
  MODIFY status ENUM('active', 'pending', 'rejected', 'suspended') NOT NULL DEFAULT 'active';

ALTER TABLE properties
  MODIFY status ENUM('draft', 'pending', 'published', 'rejected', 'archived', 'paused', 'deleted') NOT NULL DEFAULT 'draft';

ALTER TABLE bookings
  MODIFY status ENUM('pending_admin', 'pending_payment', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending_admin',
  MODIFY payment_status ENUM('not_paid', 'test_paid', 'refunded') NOT NULL DEFAULT 'not_paid';

ALTER TABLE payments
  MODIFY status ENUM('test_pending', 'test_success', 'test_failed', 'test_refunded') NOT NULL;

CREATE TABLE IF NOT EXISTS property_favorites (
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

CREATE TABLE IF NOT EXISTS property_change_requests (
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
