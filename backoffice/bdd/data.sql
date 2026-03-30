USE site_info;

-- 1. Insertion d'un utilisateur (Administrateur/Journaliste)
-- Note : Dans un vrai projet, le mot de passe serait haché avec password_hash() en PHP
INSERT INTO user (username, email, password, role) VALUES 
('Admin', 'admin@gmail.com', '$2y$10$JkEMqgUJCBVp.CA.aNCs1.iccEeAkfI2nFuly0aMLLVZlozE5X9PW', 'admin');

-- 2. Insertion des types de sources
INSERT INTO type_source (name) VALUES 
('Agence Officielle'),
('Média International'),
('Analyste Indépendant');

-- 3. Insertion des sources
INSERT INTO source (name, url, logo_url, type_id) VALUES 
('IRNA', 'https://www.irna.ir', 'https://logo.clearbit.com/irna.ir', 1),
('Reuters', 'https://www.reuters.com', 'https://logo.clearbit.com/reuters.com', 2),
('Al Jazeera', 'https://www.aljazeera.com', 'https://logo.clearbit.com/aljazeera.com', 2),
('Middle East Institute', 'https://www.mei.edu', 'https://logo.clearbit.com/mei.edu', 3);

-- 4. Insertion des catégories (avec Slugs pour le SEO)
INSERT INTO category_article (name, slug) VALUES 
('Opérations Militaires', 'operations-militaires'),
('Diplomatie', 'diplomatie'),
('Économie de Guerre', 'economie-guerre'),
('Cyber-Attaques', 'cyber-attaques');

-- 5. Insertion des articles (title en <h1> et summary/content en HTML)
-- Article 1
INSERT INTO article (title, slug, summary, content, status, source_id, category_id, user_id) VALUES 
(
    '<h1>Escalade dans le Golfe : Manœuvres navales signalées</h1>', 
    'escalade-golfe-manoeuvres-navales', 
    '<p><strong>Les tensions montent d''un cran</strong> après le déploiement de nouvelles unités navales dans le détroit d''Ormuz.</p>', 
    '<h2>Contexte</h2><p>Le commandement central a confirmé ce matin que plusieurs destroyers ont pris position suite aux rapports de mouvements suspects dans les eaux internationales.</p><h3>Impacts immédiats</h3><ul><li>Renforcement des contrôles maritimes</li><li>Hausse des coûts d''assurance transport</li><li>Risque accru d''incident régional</li></ul>', 
    'publie', 2, 1, 1
);

-- Article 2
INSERT INTO article (title, slug, summary, content, status, source_id, category_id, user_id) VALUES 
(
    '<h1>Le Rial iranien chute à un niveau historique</h1>', 
    'chute-rial-iranien-economie', 
    '<p>L''économie iranienne subit de plein fouet l''annonce de nouvelles restrictions bancaires internationales.</p>', 
    '<h2>Situation monétaire</h2><p>Dans les bazars de Téhéran, l''inquiétude grandit. En moins de 48 heures, la monnaie nationale a perdu 15% de sa valeur face au dollar.</p><p><em>Les commerçants anticipent une nouvelle vague d''inflation</em> sur les produits importés.</p>', 
    'publie', 4, 3, 1
);

-- Article 3
INSERT INTO article (title, slug, summary, content, status, source_id, category_id, user_id) VALUES 
(
    '<h1>Sommet d''urgence à Genève pour éviter le conflit</h1>', 
    'sommet-urgence-geneve-iran', 
    '<p>Les diplomates de six nations se réunissent aujourd''hui pour tenter une médiation de dernière minute.</p>', 
    '<h2>Négociations</h2><p>L''espoir d''une désescalade repose sur les épaules des médiateurs qui tentent de rétablir un canal de communication direct entre les belligérants.</p><blockquote>La priorité est d''éviter toute escalade irréversible.</blockquote>', 
    'publie', 3, 2, 1
);

-- 6. Insertion des images (liées aux articles par ID)
-- Image pour l'article 1 (Militaires)
INSERT INTO article_image (article_id, image_url, alt_text, is_main) VALUES 
(1, 'https://images.unsplash.com/photo-1517976384346-3136801d605d?w=800', 'Navire de guerre dans le brouillard', 1);

-- Images pour l'article 2 (Économie)
INSERT INTO article_image (article_id, image_url, alt_text, is_main) VALUES 
(2, 'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=800', 'Billets et monnaie', 1),
(2, 'https://images.unsplash.com/photo-1611974717483-58285a810e0e?w=800', 'Graphique boursier en baisse', 0);

-- Image pour l'article 3 (Diplomatie)
INSERT INTO article_image (article_id, image_url, alt_text, is_main) VALUES 
(3, 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?w=800', 'Salle de conférence diplomatique', 1);