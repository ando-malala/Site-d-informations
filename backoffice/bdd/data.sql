USE site_info;

-- 1. Insertion d'un utilisateur (Administrateur/Journaliste)
-- Note : Dans un vrai projet, le mot de passe serait haché avec password_hash() en PHP
INSERT INTO user (username, email, password, role) VALUES 
('Jean_Reporter', 'j.reporter@info-iran.com', '$2y$10$S8lXN.L8Xf8R8.R8Xf8R8.R8Xf8R8.R8Xf8R8.', 'admin');

-- 2. Insertion des types de sources
INSERT INTO type_source (name) VALUES 
('Agence Officielle'),
('Média International'),
('Analyste Indépendant');

-- 3. Insertion des sources
INSERT INTO source (name, url, type_id) VALUES 
('IRNA', 'https://www.irna.ir', 1),
('Reuters', 'https://www.reuters.com', 2),
('Al Jazeera', 'https://www.aljazeera.com', 2),
('Middle East Institute', 'https://www.mei.edu', 3);

-- 4. Insertion des catégories (avec Slugs pour le SEO)
INSERT INTO category_article (name, slug) VALUES 
('Opérations Militaires', 'operations-militaires'),
('Diplomatie', 'diplomatie'),
('Économie de Guerre', 'economie-guerre'),
('Cyber-Attaques', 'cyber-attaques');

-- 5. Insertion des articles (sur le thème Guerre/Tensions en Iran)
-- Article 1
INSERT INTO article (title, slug, summary, content, status, source_id, category_id, user_id) VALUES 
(
    'Escalade dans le Golfe : Manœuvres navales signalées', 
    'escalade-golfe-manoeuvres-navales', 
    'Les tensions montent d''un cran après le déploiement de nouvelles unités navales dans le détroit d''Ormuz.', 
    'Le commandement central a confirmé ce matin que plusieurs destroyers ont pris position suite aux rapports de mouvements suspects dans les eaux internationales...', 
    'publie', 2, 1, 1
);

-- Article 2
INSERT INTO article (title, slug, summary, content, status, source_id, category_id, user_id) VALUES 
(
    'Le Rial iranien chute à un niveau historique', 
    'chute-rial-iranien-economie', 
    'L''économie iranienne subit de plein fouet l''annonce de nouvelles restrictions bancaires internationales.', 
    'Dans les bazars de Téhéran, l''inquiétude grandit. En moins de 48 heures, la monnaie nationale a perdu 15% de sa valeur face au dollar, provoquant une hausse des prix...', 
    'publie', 4, 3, 1
);

-- Article 3
INSERT INTO article (title, slug, summary, content, status, source_id, category_id, user_id) VALUES 
(
    'Sommet d''urgence à Genève pour éviter le conflit', 
    'sommet-urgence-geneve-iran', 
    'Les diplomates de six nations se réunissent aujourd''hui pour tenter une médiation de dernière minute.', 
    'L''espoir d''une désescalade repose sur les épaules des médiateurs qui tentent de rétablir un canal de communication direct entre les belligérants...', 
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