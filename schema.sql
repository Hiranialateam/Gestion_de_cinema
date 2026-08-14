-- Base de données pour l'application CinéManager
CREATE DATABASE IF NOT EXISTS cinema_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cinema_db;

-- Table : Film
CREATE TABLE IF NOT EXISTS film (
    id_film INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    categorie VARCHAR(100) NOT NULL,
    duree VARCHAR(50) DEFAULT '120 min',
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table : Salle
CREATE TABLE IF NOT EXISTS salle (
    id_salle INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(50) NOT NULL,
    capacite INT NOT NULL,
    plan VARCHAR(255) DEFAULT 'Standard VIP'
) ENGINE=InnoDB;

-- Table : Seance
CREATE TABLE IF NOT EXISTS seance (
    id_seance INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    horaire TIME NOT NULL,
    id_film INT NOT NULL,
    id_salle INT NOT NULL,
    FOREIGN KEY (id_film) REFERENCES film(id_film) ON DELETE CASCADE,
    FOREIGN KEY (id_salle) REFERENCES salle(id_salle) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table : Client
CREATE TABLE IF NOT EXISTS client (
    id_client INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    contact VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- Table : Reservation
CREATE TABLE IF NOT EXISTS reservation (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_client INT NOT NULL,
    id_seance INT NOT NULL,
    FOREIGN KEY (id_client) REFERENCES client(id_client) ON DELETE CASCADE,
    FOREIGN KEY (id_seance) REFERENCES seance(id_seance) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table : Billet
CREATE TABLE IF NOT EXISTS billet (
    id_billet INT AUTO_INCREMENT PRIMARY KEY,
    numero_place VARCHAR(20) NOT NULL,
    tarif DECIMAL(10,2) NOT NULL,
    id_reservation INT NOT NULL,
    FOREIGN KEY (id_reservation) REFERENCES reservation(id_reservation) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insertion de données de démonstration
INSERT INTO film (titre, categorie, duree, description) VALUES
('Inception', 'Science-Fiction', '148 min', 'Un voleur habile s\'infiltre dans les rêves pour dérober des secrets d\'entreprise.'),
('Dune : Deuxième Partie', 'Aventure / Sci-Fi', '166 min', 'Paul Atréides s\'unit aux Fremen pour mener la révolte contre les conspirateurs.'),
('Oppenheimer', 'Biopic / Drame', '180 min', 'L\'histoire du physicien J. Robert Oppenheimer et de la création de la bombe atomique.');

INSERT INTO salle (numero, capacite, plan) VALUES
('Salle 1 - IMAX Premium', 250, 'Fauteuils inclinables'),
('Salle 2 - VIP Lounge', 60, 'Sofa Duo'),
('Salle 3 - Dolby Atmos', 180, 'Standard Gold');

INSERT INTO seance (date, horaire, id_film, id_salle) VALUES
(CURDATE(), '18:30:00', 1, 1),
(CURDATE(), '21:00:00', 2, 2),
(DATE_ADD(CURDATE(), INTERVAL 1 DAY), '15:00:00', 3, 3);
