USE atypikhouse;

INSERT INTO users (id, first_name, last_name, email, password_hash, phone, role, status, created_at, updated_at) VALUES
(1, 'Alice', 'Admin', 'admin@atypikhouse.test', '$2y$10$Oat6mjhrzZM6xSGMb.0xd.l3yDcO5HJyL03kJChOPFdW3ijr3P/42', '0102030405', 'admin', 'active', NOW(), NOW()),
(2, 'Olivier', 'Hôte', 'owner@atypikhouse.test', '$2y$10$A/uo/afqjiF6bPBqTSFMPOOIclPt4OPCucaY5oVQjbnXjc48tdp1C', '0601020304', 'owner', 'active', NOW(), NOW()),
(3, 'Tania', 'Voyage', 'tenant@atypikhouse.test', '$2y$10$FUhhwW14yTv8rI388a3CEens3nO1sA5qQJB8bv0LYROJzF7TMQkqq', '0609080706', 'tenant', 'active', NOW(), NOW()),
(4, 'Camille', 'Martin', 'camille.martin@atypikhouse.test', '$2y$10$FUhhwW14yTv8rI388a3CEens3nO1sA5qQJB8bv0LYROJzF7TMQkqq', '0600000004', 'tenant', 'active', NOW(), NOW()),
(5, 'Hugo', 'Bernard', 'hugo.bernard@atypikhouse.test', '$2y$10$FUhhwW14yTv8rI388a3CEens3nO1sA5qQJB8bv0LYROJzF7TMQkqq', '0600000005', 'tenant', 'active', NOW(), NOW()),
(6, 'Léa', 'Moreau', 'lea.moreau@atypikhouse.test', '$2y$10$FUhhwW14yTv8rI388a3CEens3nO1sA5qQJB8bv0LYROJzF7TMQkqq', '0600000006', 'tenant', 'active', NOW(), NOW()),
(7, 'Emma', 'Laurent', 'emma.laurent@atypikhouse.test', '$2y$10$FUhhwW14yTv8rI388a3CEens3nO1sA5qQJB8bv0LYROJzF7TMQkqq', '0600000007', 'tenant', 'active', NOW(), NOW()),
(8, 'Nicolas', 'Petit', 'nicolas.petit@atypikhouse.test', '$2y$10$FUhhwW14yTv8rI388a3CEens3nO1sA5qQJB8bv0LYROJzF7TMQkqq', '0600000008', 'tenant', 'active', NOW(), NOW()),
(9, 'Manon', 'Dubois', 'manon.dubois@atypikhouse.test', '$2y$10$FUhhwW14yTv8rI388a3CEens3nO1sA5qQJB8bv0LYROJzF7TMQkqq', '0600000009', 'tenant', 'active', NOW(), NOW()),
(10, 'Clara', 'Rousseau', 'clara.rousseau@atypikhouse.test', '$2y$10$FUhhwW14yTv8rI388a3CEens3nO1sA5qQJB8bv0LYROJzF7TMQkqq', '0600000010', 'tenant', 'active', NOW(), NOW()),
(11, 'Antoine', 'Girard', 'antoine.girard@atypikhouse.test', '$2y$10$FUhhwW14yTv8rI388a3CEens3nO1sA5qQJB8bv0LYROJzF7TMQkqq', '0600000011', 'tenant', 'active', NOW(), NOW());

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

INSERT INTO property_images (property_id, image_path, alt_text, is_main, created_at) VALUES
(1, 'assets/img/properties/cabane-spa-pierrefonds-01.avif', 'Cabane en bois avec terrasse et bain nordique dans une forêt tempérée près de Pierrefonds', 1, NOW()),
(1, 'assets/img/properties/cabane-passerelle.jpg', 'Cabane vitrée perchée reliée par une passerelle dans la forêt au coucher du soleil', 0, NOW()),
(2, 'assets/img/properties/roulotte-familiale-campagne-01.avif', 'Hébergement insolite en bois dans un jardin naturel en Hauts-de-France', 1, NOW()),
(3, 'assets/img/properties/cabane-flottante.jpg', 'Cabane en bois sur les eaux calmes d''un lac de montagne', 1, NOW()),
(4, 'assets/img/properties/tiny-house-ecologique-01.jpg', 'Tiny house écologique arrondie en bois avec terrasse dans un environnement naturel', 1, NOW()),
(5, 'assets/img/properties/dome-romantique-nature-01.jpg', 'Dôme insolite installé sur une terrasse bois au milieu de la végétation', 1, NOW()),
(6, 'assets/img/properties/roulotte-familiale-campagne-01.avif', 'Petit hébergement familial en bois avec terrasse dans un jardin arboré', 1, NOW()),
(7, 'assets/img/properties/refuge-foret.jpg', 'Lodge en bois sobre au coeur d''une forêt tempérée', 1, NOW()),
(8, 'assets/img/properties/maison-perchee-fontainebleau-01.jpg', 'Maison perchée haut de gamme éclairée dans une forêt au crépuscule', 1, NOW()),
(8, 'assets/img/properties/cabane-passerelle.jpg', 'Passerelle suspendue menant à une maison perchée premium', 0, NOW());

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
(2, 4, 3, DATE_ADD(CURDATE(), INTERVAL 12 DAY), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 2, 2, 196.00, 25.00, 221.00, 'confirmed', 'test_paid', NOW(), NOW()),
(101, 1, 4, DATE_SUB(CURDATE(), INTERVAL 96 DAY), DATE_SUB(CURDATE(), INTERVAL 94 DAY), 2, 2, 370.00, 35.00, 405.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 96 DAY), DATE_SUB(NOW(), INTERVAL 94 DAY)),
(102, 1, 5, DATE_SUB(CURDATE(), INTERVAL 73 DAY), DATE_SUB(CURDATE(), INTERVAL 72 DAY), 1, 2, 185.00, 35.00, 220.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 73 DAY), DATE_SUB(NOW(), INTERVAL 72 DAY)),
(103, 2, 6, DATE_SUB(CURDATE(), INTERVAL 88 DAY), DATE_SUB(CURDATE(), INTERVAL 86 DAY), 2, 3, 230.00, 20.00, 250.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 88 DAY), DATE_SUB(NOW(), INTERVAL 86 DAY)),
(104, 2, 7, DATE_SUB(CURDATE(), INTERVAL 61 DAY), DATE_SUB(CURDATE(), INTERVAL 60 DAY), 1, 2, 115.00, 20.00, 135.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 61 DAY), DATE_SUB(NOW(), INTERVAL 60 DAY)),
(105, 3, 8, DATE_SUB(CURDATE(), INTERVAL 82 DAY), DATE_SUB(CURDATE(), INTERVAL 80 DAY), 2, 2, 320.00, 30.00, 350.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 82 DAY), DATE_SUB(NOW(), INTERVAL 80 DAY)),
(106, 3, 9, DATE_SUB(CURDATE(), INTERVAL 48 DAY), DATE_SUB(CURDATE(), INTERVAL 47 DAY), 1, 2, 160.00, 30.00, 190.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 48 DAY), DATE_SUB(NOW(), INTERVAL 47 DAY)),
(107, 4, 10, DATE_SUB(CURDATE(), INTERVAL 91 DAY), DATE_SUB(CURDATE(), INTERVAL 89 DAY), 2, 2, 196.00, 25.00, 221.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 91 DAY), DATE_SUB(NOW(), INTERVAL 89 DAY)),
(108, 4, 11, DATE_SUB(CURDATE(), INTERVAL 55 DAY), DATE_SUB(CURDATE(), INTERVAL 54 DAY), 1, 2, 98.00, 25.00, 123.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 55 DAY), DATE_SUB(NOW(), INTERVAL 54 DAY)),
(109, 5, 4, DATE_SUB(CURDATE(), INTERVAL 76 DAY), DATE_SUB(CURDATE(), INTERVAL 74 DAY), 2, 2, 344.00, 35.00, 379.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 76 DAY), DATE_SUB(NOW(), INTERVAL 74 DAY)),
(110, 5, 5, DATE_SUB(CURDATE(), INTERVAL 39 DAY), DATE_SUB(CURDATE(), INTERVAL 38 DAY), 1, 2, 172.00, 35.00, 207.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 39 DAY), DATE_SUB(NOW(), INTERVAL 38 DAY)),
(111, 6, 6, DATE_SUB(CURDATE(), INTERVAL 69 DAY), DATE_SUB(CURDATE(), INTERVAL 67 DAY), 2, 4, 250.00, 30.00, 280.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 69 DAY), DATE_SUB(NOW(), INTERVAL 67 DAY)),
(112, 6, 7, DATE_SUB(CURDATE(), INTERVAL 34 DAY), DATE_SUB(CURDATE(), INTERVAL 33 DAY), 1, 3, 125.00, 30.00, 155.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 34 DAY), DATE_SUB(NOW(), INTERVAL 33 DAY)),
(113, 7, 8, DATE_SUB(CURDATE(), INTERVAL 84 DAY), DATE_SUB(CURDATE(), INTERVAL 82 DAY), 2, 4, 290.00, 28.00, 318.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 84 DAY), DATE_SUB(NOW(), INTERVAL 82 DAY)),
(114, 7, 9, DATE_SUB(CURDATE(), INTERVAL 43 DAY), DATE_SUB(CURDATE(), INTERVAL 42 DAY), 1, 2, 145.00, 28.00, 173.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 43 DAY), DATE_SUB(NOW(), INTERVAL 42 DAY)),
(115, 8, 10, DATE_SUB(CURDATE(), INTERVAL 98 DAY), DATE_SUB(CURDATE(), INTERVAL 96 DAY), 2, 4, 440.00, 45.00, 485.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 98 DAY), DATE_SUB(NOW(), INTERVAL 96 DAY)),
(116, 8, 11, DATE_SUB(CURDATE(), INTERVAL 52 DAY), DATE_SUB(CURDATE(), INTERVAL 51 DAY), 1, 2, 220.00, 45.00, 265.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 52 DAY), DATE_SUB(NOW(), INTERVAL 51 DAY)),
(117, 8, 4, DATE_SUB(CURDATE(), INTERVAL 28 DAY), DATE_SUB(CURDATE(), INTERVAL 26 DAY), 2, 3, 440.00, 45.00, 485.00, 'completed', 'test_paid', DATE_SUB(NOW(), INTERVAL 28 DAY), DATE_SUB(NOW(), INTERVAL 26 DAY));

INSERT INTO payments (booking_id, provider, test_transaction_id, amount, status, created_at) VALUES
(1, 'simulation', 'TEST-DEMO-0001', 405.00, 'test_success', NOW()),
(2, 'simulation', 'TEST-DEMO-0002', 221.00, 'test_success', NOW()),
(101, 'simulation', 'TEST-DEMO-0101', 405.00, 'test_success', DATE_SUB(NOW(), INTERVAL 94 DAY)),
(102, 'simulation', 'TEST-DEMO-0102', 220.00, 'test_success', DATE_SUB(NOW(), INTERVAL 72 DAY)),
(103, 'simulation', 'TEST-DEMO-0103', 250.00, 'test_success', DATE_SUB(NOW(), INTERVAL 86 DAY)),
(104, 'simulation', 'TEST-DEMO-0104', 135.00, 'test_success', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(105, 'simulation', 'TEST-DEMO-0105', 350.00, 'test_success', DATE_SUB(NOW(), INTERVAL 80 DAY)),
(106, 'simulation', 'TEST-DEMO-0106', 190.00, 'test_success', DATE_SUB(NOW(), INTERVAL 47 DAY)),
(107, 'simulation', 'TEST-DEMO-0107', 221.00, 'test_success', DATE_SUB(NOW(), INTERVAL 89 DAY)),
(108, 'simulation', 'TEST-DEMO-0108', 123.00, 'test_success', DATE_SUB(NOW(), INTERVAL 54 DAY)),
(109, 'simulation', 'TEST-DEMO-0109', 379.00, 'test_success', DATE_SUB(NOW(), INTERVAL 74 DAY)),
(110, 'simulation', 'TEST-DEMO-0110', 207.00, 'test_success', DATE_SUB(NOW(), INTERVAL 38 DAY)),
(111, 'simulation', 'TEST-DEMO-0111', 280.00, 'test_success', DATE_SUB(NOW(), INTERVAL 67 DAY)),
(112, 'simulation', 'TEST-DEMO-0112', 155.00, 'test_success', DATE_SUB(NOW(), INTERVAL 33 DAY)),
(113, 'simulation', 'TEST-DEMO-0113', 318.00, 'test_success', DATE_SUB(NOW(), INTERVAL 82 DAY)),
(114, 'simulation', 'TEST-DEMO-0114', 173.00, 'test_success', DATE_SUB(NOW(), INTERVAL 42 DAY)),
(115, 'simulation', 'TEST-DEMO-0115', 485.00, 'test_success', DATE_SUB(NOW(), INTERVAL 96 DAY)),
(116, 'simulation', 'TEST-DEMO-0116', 265.00, 'test_success', DATE_SUB(NOW(), INTERVAL 51 DAY)),
(117, 'simulation', 'TEST-DEMO-0117', 485.00, 'test_success', DATE_SUB(NOW(), INTERVAL 26 DAY));

INSERT INTO reviews (booking_id, property_id, tenant_id, rating, comment, status, created_at, updated_at) VALUES
(1, 1, 3, 5, 'Très belle expérience fictive, l’ambiance nature est réussie.', 'published', DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 7 DAY)),
(101, 1, 4, 5, 'Cabane très agréable pour déconnecter. La terrasse et le bain nordique donnent vraiment l’impression d’être seuls en forêt.', 'published', DATE_SUB(NOW(), INTERVAL 92 DAY), DATE_SUB(NOW(), INTERVAL 92 DAY)),
(102, 1, 5, 4, 'Séjour court mais très reposant. Le logement est chaleureux, bien pensé et le cadre près de Pierrefonds fonctionne très bien.', 'published', DATE_SUB(NOW(), INTERVAL 70 DAY), DATE_SUB(NOW(), INTERVAL 70 DAY)),
(103, 2, 6, 5, 'Ambiance cocooning et originale. La nuit sous la toile change vraiment d’un hébergement classique, tout en restant confortable.', 'published', DATE_SUB(NOW(), INTERVAL 84 DAY), DATE_SUB(NOW(), INTERVAL 84 DAY)),
(104, 2, 7, 4, 'Adresse simple et dépaysante pour un week-end nature. Nous avons apprécié le calme et l’esprit chaleureux de la yourte.', 'published', DATE_SUB(NOW(), INTERVAL 58 DAY), DATE_SUB(NOW(), INTERVAL 58 DAY)),
(105, 3, 8, 5, 'Réveil au bord de l’eau très agréable. La cabane flottante donne une vraie sensation de pause, avec beaucoup de calme.', 'published', DATE_SUB(NOW(), INTERVAL 78 DAY), DATE_SUB(NOW(), INTERVAL 78 DAY)),
(106, 3, 9, 4, 'Expérience insolite réussie. Le ponton et la vue sur l’eau rendent le séjour très reposant, parfait pour une nuit à deux.', 'published', DATE_SUB(NOW(), INTERVAL 45 DAY), DATE_SUB(NOW(), INTERVAL 45 DAY)),
(107, 4, 10, 5, 'Tiny house très bien agencée. On sent l’effort sur le côté écologique sans perdre le confort nécessaire pour un court séjour.', 'published', DATE_SUB(NOW(), INTERVAL 87 DAY), DATE_SUB(NOW(), INTERVAL 87 DAY)),
(108, 4, 11, 4, 'Petit espace mais très fonctionnel. Le logement est propre, lumineux et idéal pour tester une parenthèse plus sobre.', 'published', DATE_SUB(NOW(), INTERVAL 52 DAY), DATE_SUB(NOW(), INTERVAL 52 DAY)),
(109, 5, 4, 5, 'Le dôme est vraiment dépaysant. On profite bien de la lumière et de la vue, avec une ambiance glamping très agréable.', 'published', DATE_SUB(NOW(), INTERVAL 72 DAY), DATE_SUB(NOW(), INTERVAL 72 DAY)),
(110, 5, 5, 4, 'Très bon moment dans un cadre ouvert et calme. L’expérience est originale sans être compliquée à organiser.', 'published', DATE_SUB(NOW(), INTERVAL 36 DAY), DATE_SUB(NOW(), INTERVAL 36 DAY)),
(111, 6, 6, 4, 'Roulotte familiale charmante, simple et conviviale. Les enfants ont aimé le côté aventure sans partir trop loin.', 'published', DATE_SUB(NOW(), INTERVAL 65 DAY), DATE_SUB(NOW(), INTERVAL 65 DAY)),
(112, 6, 7, 5, 'Logement chaleureux et bien adapté à une petite escapade en famille. Le jardin et l’ambiance campagne sont très agréables.', 'published', DATE_SUB(NOW(), INTERVAL 31 DAY), DATE_SUB(NOW(), INTERVAL 31 DAY)),
(113, 7, 8, 4, 'Lodge confortable et calme, idéal pour couper avec le rythme de la semaine. Les produits locaux ajoutent une touche appréciable.', 'published', DATE_SUB(NOW(), INTERVAL 80 DAY), DATE_SUB(NOW(), INTERVAL 80 DAY)),
(114, 7, 9, 5, 'Très belle parenthèse à la campagne. Le logement est sobre, agréable et correspond bien à l’esprit séjour responsable.', 'published', DATE_SUB(NOW(), INTERVAL 40 DAY), DATE_SUB(NOW(), INTERVAL 40 DAY)),
(115, 8, 10, 5, 'Maison perchée superbe pour un week-end premium. La terrasse, le bois et la vue créent une vraie expérience mémorable.', 'published', DATE_SUB(NOW(), INTERVAL 94 DAY), DATE_SUB(NOW(), INTERVAL 94 DAY)),
(116, 8, 11, 4, 'Très belle adresse fictive, confortable et immersive. On se projette facilement dans une escapade nature près de Fontainebleau.', 'published', DATE_SUB(NOW(), INTERVAL 49 DAY), DATE_SUB(NOW(), INTERVAL 49 DAY)),
(117, 8, 4, 5, 'Expérience très réussie en hauteur. Le logement donne une impression de refuge premium sans perdre le lien avec la forêt.', 'published', DATE_SUB(NOW(), INTERVAL 24 DAY), DATE_SUB(NOW(), INTERVAL 24 DAY));

INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, status, author_id, created_at, updated_at) VALUES
('Week-end insolite proche de Paris', 'week-end-insolite-proche-de-paris', 'Idées fictives pour préparer une escapade nature à moins de deux heures de Paris.', 'Entre cabanes perchées, tiny houses et lodges à la campagne, un week-end insolite proche de Paris permet de changer de rythme sans partir loin. Cet article de démonstration présente les bénéfices d’un séjour nature, les critères de choix et les bonnes pratiques pour un tourisme plus responsable.', 'assets/img/properties/cabane-panorama.jpg', 'published', 1, NOW(), NOW()),
('Comment choisir un hébergement éco-responsable', 'comment-choisir-un-hebergement-eco-responsable', 'Quelques repères simples pour comparer les logements insolites.', 'Regardez les matériaux, la gestion de l’eau, les mobilités douces, la capacité adaptée et la transparence de l’hôte. Sur AtypikHouse, ces informations sont structurées pour aider la décision.', 'assets/img/properties/refuge-foret.jpg', 'published', 1, NOW(), NOW());

INSERT INTO contact_messages (name, email, subject, message, status, created_at) VALUES
('Demo Contact', 'contact@example.test', 'Question sur le projet', 'Message fictif visible dans le tableau de bord administrateur.', 'new', NOW());

INSERT INTO audit_logs (user_id, action, entity_type, entity_id, ip_address, created_at) VALUES
(1, 'seed_import', 'database', NULL, '127.0.0.1', NOW());
