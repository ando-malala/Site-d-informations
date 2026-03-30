-- Suppression de la base si elle existe pour repartir à neuf (optionnel)
DROP DATABASE IF EXISTS site_info;
CREATE DATABASE site_info CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE site_info;

-- 1. Table Utilisateurs (avec ajout d'email et de rôle)
CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Stockage du hash (password_hash en PHP)
    role ENUM('admin', 'editeur') DEFAULT 'editeur',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Table Types de source
CREATE TABLE type_source (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- 3. Table Sources (Ajout d'un logo optionnel)
CREATE TABLE source (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    url VARCHAR(255) NOT NULL,
    logo_url VARCHAR(255), 
    type_id INT,
    FOREIGN KEY (type_id) REFERENCES type_source(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 4. Table Catégories
CREATE TABLE category_article (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE -- Pour des URLs propres ex: /categorie/operations-militaires
) ENGINE=InnoDB;

-- 5. Table Articles (Optimisée pour le contenu et le SEO)
CREATE TABLE article (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE, -- URL de l'article
    summary TEXT, -- Résumé court pour la page d'accueil (Accroche)
    content LONGTEXT NOT NULL,
    status ENUM('brouillon', 'publie', 'archive') DEFAULT 'brouillon',
    source_id INT,
    category_id INT,
    user_id INT, -- Auteur de l'article
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (source_id) REFERENCES source(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES category_article(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 6. Table Images (avec gestion de l'image principale)
CREATE TABLE article_image (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id INT,
    image_url VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255), -- Pour l'accessibilité et le SEO
    is_main BOOLEAN DEFAULT FALSE, -- Définit l'image à afficher en couverture
    FOREIGN KEY (article_id) REFERENCES article(id) ON DELETE CASCADE
) ENGINE=InnoDB;