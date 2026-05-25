USE atypikhouse;

INSERT INTO users (id, first_name, last_name, email, password_hash, phone, role, status, created_at, updated_at) VALUES
(1, 'Alice', 'Admin', 'admin@atypikhouse.test', '$2y$10$Oat6mjhrzZM6xSGMb.0xd.l3yDcO5HJyL03kJChOPFdW3ijr3P/42', '0102030405', 'admin', 'active', NOW(), NOW()),
(2, 'Olivier', 'Hôte', 'owner@atypikhouse.test', '$2y$10$A/uo/afqjiF6bPBqTSFMPOOIclPt4OPCucaY5oVQjbnXjc48tdp1C', '0601020304', 'owner', 'active', NOW(), NOW()),
(3, 'Tania', 'Voyage', 'tenant@atypikhouse.test', '$2y$10$FUhhwW14yTv8rI388a3CEens3nO1sA5qQJB8bv0LYROJzF7TMQkqq', '0609080706', 'tenant', 'active', NOW(), NOW());

INSERT INTO owner_profiles (user_id, company_name, address, city, postal_code, country, description, verification_status, created_at, updated_at) VALUES
(2, 'Forêts & Refuges', '12 rue du Château', 'Pierrefonds', '60350', 'France', 'Exploitant fictif de logements insolites éco-responsables.', 'approved', NOW(), NOW());

INSERT INTO properties (id, owner_id, title, slug, type, short_description, long_description, address, city, postal_code, region, country, capacity, bedrooms, beds, bathrooms, price_per_night, cleaning_fee, eco_score, status, created_at, updated_at) VALUES
(1, 2, 'Cabane dans les arbres avec spa', 'cabane-dans-les-arbres-avec-spa', 'treehouse', 'Une cabane perchée avec bain nordique et vue forêt.', 'Séjour fictif premium en hauteur, pensé pour une parenthèse nature proche de Pierrefonds. Terrasse, ambiance bois, réveil au chant des oiseaux et spa privatif de démonstration.', 'Chemin des chênes', 'Pierrefonds', '60350', 'Hauts-de-France', 'France', 2, 1, 1, 1, 185.00, 35.00, 5, 'published', NOW(), NOW()),
(2, 2, 'Yourte nature en forêt', 'yourte-nature-en-foret', 'yurt', 'Yourte chaleureuse pour une nuit douce sous les étoiles.', 'Une yourte fictive installée dans un environnement boisé, idéale pour découvrir un hébergement circulaire, simple et dépaysant.', 'Route forestière', 'Compiègne', '60200', 'Hauts-de-France', 'France', 4, 1, 3, 1, 115.00, 20.00, 4, 'published', NOW(), NOW()),
(3, 2, 'Cabane flottante sur lac', 'cabane-flottante-sur-lac', 'floating_cabin', 'Cabane flottante avec ponton privé et calme absolu.', 'Un logement fictif posé sur l’eau, conçu pour illustrer un parcours de réservation immersif sans transaction réelle.', 'Lac des songes', 'Chantilly', '60500', 'Hauts-de-France', 'France', 2, 1, 1, 1, 160.00, 30.00, 4, 'published', NOW(), NOW()),
(4, 2, 'Tiny house écologique', 'tiny-house-ecologique', 'tiny_house', 'Mini-maison confortable avec faible empreinte.', 'Tiny house fictive équipée pour tester la recherche, la réservation et la gestion de disponibilités.', 'Rue des prairies', 'Senlis', '60300', 'Hauts-de-France', 'France', 3, 1, 2, 1, 98.00, 25.00, 5, 'published', NOW(), NOW()),
(5, 2, 'Dôme romantique avec vue', 'dome-romantique-avec-vue', 'dome', 'Dôme géodésique pour observer le ciel et se ressourcer.', 'Un cocon fictif avec vue ouverte, parfait pour la projection émotionnelle et les séjours de courte durée.', 'Colline verte', 'Annecy', '74000', 'Auvergne-Rhône-Alpes', 'France', 2, 1, 1, 1, 172.00, 35.00, 4, 'published', NOW(), NOW()),
(6, 2, 'Roulotte familiale', 'roulotte-familiale', 'other', 'Roulotte colorée pour une escapade en famille.', 'Logement fictif familial, simple à réserver et adapté aux voyageurs qui cherchent une expérience douce et originale.', 'Domaine des vergers', 'Tours', '37000', 'Centre-Val de Loire', 'France', 5, 2, 4, 1, 125.00, 30.00, 3, 'published', NOW(), NOW()),
(7, 2, 'Lodge insolite en campagne', 'lodge-insolite-en-campagne', 'other', 'Lodge confortable au milieu des champs.', 'Un lodge fictif pensé pour la déconnexion, les week-ends proches de Paris et les séjours responsables.', 'Chemin rural', 'Provins', '77160', 'Île-de-France', 'France', 4, 2, 3, 1, 145.00, 28.00, 4, 'published', NOW(), NOW()),
(8, 2, 'Maison perchée premium', 'maison-perchee-premium', 'treehouse', 'Maison perchée haut de gamme avec grande terrasse.', 'Hébergement fictif premium pour présenter les pages détail, les avis modérés et le parcours de réservation test.', 'Bois des brumes', 'Fontainebleau', '77300', 'Île-de-France', 'France', 4, 2, 2, 1, 220.00, 45.00, 5, 'published', NOW(), NOW());

INSERT INTO property_images (property_id, image_path, alt_text, is_main, created_at)
SELECT id, 'assets/img/properties/default-placeholder.svg', CONCAT('Image illustrative de ', title), 1, NOW() FROM properties;

INSERT INTO property_amenities (property_id, amenity_name) VALUES
(1,'Spa'),(1,'Terrasse'),(1,'Petit-déjeuner'),(2,'Poêle'),(2,'Observation des étoiles'),(2,'Parking'),
(3,'Ponton privé'),(3,'Vue lac'),(3,'Calme'),(4,'Cuisine équipée'),(4,'Toilettes sèches'),(4,'Wifi'),
(5,'Vue panoramique'),(5,'Lit queen size'),(5,'Chauffage'),(6,'Jeux enfants'),(6,'Cuisine'),(6,'Jardin'),
(7,'Cheminée'),(7,'Vélos'),(7,'Produits locaux'),(8,'Grande terrasse'),(8,'Bain nordique'),(8,'Panier accueil');

INSERT INTO property_availabilities (property_id, date, is_available, price_override, created_at, updated_at)
SELECT p.id, DATE_ADD(CURDATE(), INTERVAL seq.n DAY), 1, NULL, NOW(), NOW()
FROM properties p
JOIN (
  SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
) seq;

INSERT INTO bookings (id, property_id, tenant_id, start_date, end_date, nights, guests_count, subtotal, cleaning_fee, total_price, status, payment_status, created_at, updated_at) VALUES
(1, 1, 3, DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 8 DAY), 2, 2, 370.00, 35.00, 405.00, 'completed', 'test_paid', NOW(), NOW()),
(2, 4, 3, DATE_ADD(CURDATE(), INTERVAL 12 DAY), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 2, 2, 196.00, 25.00, 221.00, 'confirmed', 'test_paid', NOW(), NOW());

INSERT INTO payments (booking_id, provider, test_transaction_id, amount, status, created_at) VALUES
(1, 'simulation', 'TEST-DEMO-0001', 405.00, 'test_success', NOW()),
(2, 'simulation', 'TEST-DEMO-0002', 221.00, 'test_success', NOW());

INSERT INTO reviews (booking_id, property_id, tenant_id, rating, comment, status, created_at, updated_at) VALUES
(1, 1, 3, 5, 'Très belle expérience fictive, l’ambiance nature est réussie.', 'published', NOW(), NOW());

INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, status, author_id, created_at, updated_at) VALUES
('Week-end insolite proche de Paris', 'week-end-insolite-proche-de-paris', 'Idées fictives pour préparer une escapade nature à moins de deux heures de Paris.', 'Entre cabanes perchées, tiny houses et lodges à la campagne, un week-end insolite proche de Paris permet de changer de rythme sans partir loin. Cet article de démonstration présente les bénéfices d’un séjour nature, les critères de choix et les bonnes pratiques pour un tourisme plus responsable.', 'assets/img/properties/default-placeholder.svg', 'published', 1, NOW(), NOW()),
('Comment choisir un hébergement éco-responsable', 'comment-choisir-un-hebergement-eco-responsable', 'Quelques repères simples pour comparer les logements insolites.', 'Regardez les matériaux, la gestion de l’eau, les mobilités douces, la capacité adaptée et la transparence de l’hôte. Sur AtypikHouse, ces informations sont structurées pour aider la décision.', 'assets/img/properties/default-placeholder.svg', 'published', 1, NOW(), NOW());

INSERT INTO contact_messages (name, email, subject, message, status, created_at) VALUES
('Demo Contact', 'contact@example.test', 'Question sur le projet', 'Message fictif visible dans le tableau de bord administrateur.', 'new', NOW());

INSERT INTO audit_logs (user_id, action, entity_type, entity_id, ip_address, created_at) VALUES
(1, 'seed_import', 'database', NULL, '127.0.0.1', NOW());
