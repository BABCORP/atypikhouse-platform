-- Mise à jour non destructive pour une base MySQL déjà existante avant déploiement Render.
-- Pour une nouvelle base vide, importer plutôt database/schema.sql puis database/seed.sql.
-- Vérifier le nom de base avec `USE votre_base;` si votre client SQL ne le sélectionne pas déjà.

ALTER TABLE users
  MODIFY status ENUM('active', 'pending', 'rejected', 'suspended') NOT NULL DEFAULT 'active';

ALTER TABLE properties
  MODIFY status ENUM('draft', 'pending', 'published', 'rejected', 'archived', 'paused', 'deleted') NOT NULL DEFAULT 'draft';

ALTER TABLE bookings
  MODIFY status ENUM('pending_admin', 'pending_payment', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending_admin',
  MODIFY payment_status ENUM('not_paid', 'test_paid', 'test_failed', 'refunded') NOT NULL DEFAULT 'not_paid';

ALTER TABLE payments
  MODIFY status ENUM('test_pending', 'test_success', 'test_failed', 'test_refunded') NOT NULL;

-- Normalise les comptes de démonstration sans toucher aux comptes créés par les utilisateurs.
-- Identifiants attendus :
-- admin@atypikhouse.fr / Admin123!
-- proprietaire@atypikhouse.fr / Owner123!
-- locataire@atypikhouse.fr / Tenant123!
INSERT INTO users (first_name, last_name, email, password_hash, phone, role, status, created_at, updated_at) VALUES
('Alice', 'Admin', 'admin@atypikhouse.fr', '$2y$10$Oat6mjhrzZM6xSGMb.0xd.l3yDcO5HJyL03kJChOPFdW3ijr3P/42', '0102030405', 'admin', 'active', NOW(), NOW()),
('Olivier', 'Hôte', 'proprietaire@atypikhouse.fr', '$2y$10$A/uo/afqjiF6bPBqTSFMPOOIclPt4OPCucaY5oVQjbnXjc48tdp1C', '0601020304', 'owner', 'active', NOW(), NOW()),
('Tania', 'Voyage', 'locataire@atypikhouse.fr', '$2y$10$FUhhwW14yTv8rI388a3CEens3nO1sA5qQJB8bv0LYROJzF7TMQkqq', '0609080706', 'tenant', 'active', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  first_name = VALUES(first_name),
  last_name = VALUES(last_name),
  password_hash = VALUES(password_hash),
  phone = VALUES(phone),
  role = VALUES(role),
  status = VALUES(status),
  updated_at = NOW();

INSERT INTO owner_profiles (user_id, company_name, address, city, postal_code, country, description, verification_status, created_at, updated_at)
SELECT u.id, 'Forêts & Refuges', '12 rue du Château', 'Pierrefonds', '60350', 'France', 'Exploitant fictif de logements insolites éco-responsables.', 'approved', NOW(), NOW()
FROM users u
WHERE u.email = 'proprietaire@atypikhouse.fr'
  AND NOT EXISTS (
    SELECT 1
    FROM owner_profiles op
    WHERE op.user_id = u.id
  );

UPDATE owner_profiles op
JOIN users u ON u.id = op.user_id
SET op.verification_status = 'approved',
    op.updated_at = NOW()
WHERE u.email = 'proprietaire@atypikhouse.fr';

-- Corrige les réservations historiques si un ancien déploiement avait stocké seulement prix/nuit + ménage.
-- La date de départ est exclue du nombre de nuits : 2026-09-05 -> 2026-09-09 = 4 nuits.
UPDATE bookings b
JOIN properties p ON p.id = b.property_id
SET
  b.nights = DATEDIFF(b.end_date, b.start_date),
  b.subtotal = ROUND(p.price_per_night * DATEDIFF(b.end_date, b.start_date), 2),
  b.cleaning_fee = p.cleaning_fee,
  b.total_price = ROUND((p.price_per_night * DATEDIFF(b.end_date, b.start_date)) + p.cleaning_fee, 2),
  b.updated_at = NOW()
WHERE b.end_date > b.start_date;

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
