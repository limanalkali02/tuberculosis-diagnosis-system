-- ============================================================
-- DESIGN AND IMPLEMENTATION OF ONLINE DIAGNOSIS FOR TUBERCULOSIS DISEASE
-- Case Study: Bayara Hospital, Bauchi
-- Database Schema (MySQL / MariaDB)
-- Corresponds to Figure 2 (Architecture) and Figure 4 (DFD) of Chapter 3
-- ============================================================

CREATE DATABASE IF NOT EXISTS tb_diagnosis_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tb_diagnosis_db;

-- ------------------------------------------------------------
-- D1: Patient / User Database
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id        INT AUTO_INCREMENT PRIMARY KEY,
    full_name      VARCHAR(100) NOT NULL,
    email          VARCHAR(120) NOT NULL UNIQUE,
    phone          VARCHAR(20)  NOT NULL,
    gender         ENUM('Male','Female') NOT NULL,
    age            INT NOT NULL,
    password_hash  VARCHAR(255) NOT NULL,
    role           ENUM('patient','admin') NOT NULL DEFAULT 'patient',
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- D2: TB Symptom Question Bank (the "knowledge acquisition" list
-- gathered from the medical expert + literature, section 3.5)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS symptoms (
    symptom_id     INT AUTO_INCREMENT PRIMARY KEY,
    code           VARCHAR(30) NOT NULL UNIQUE,   -- e.g. COUGH_2WKS
    question_text  VARCHAR(255) NOT NULL,
    category       ENUM('core','additional','risk_factor') NOT NULL,
    weight         INT NOT NULL DEFAULT 1,        -- used by the rule engine
    display_order  INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- WHO four-symptom screen = the 4 "core" questions carry the most weight.
INSERT INTO symptoms (code, question_text, category, weight, display_order) VALUES
('COUGH_2WKS',     'Have you had a cough lasting 2 weeks or longer?',                        'core', 3, 1),
('FEVER',          'Have you had an unexplained fever?',                                      'core', 3, 2),
('NIGHT_SWEATS',   'Have you experienced night sweats?',                                      'core', 3, 3),
('WEIGHT_LOSS',    'Have you had unexplained weight loss?',                                   'core', 3, 4),
('BLOODY_SPUTUM',  'Have you coughed up blood or blood-stained sputum?',                       'additional', 3, 5),
('CHEST_PAIN',     'Have you had chest pain?',                                                 'additional', 2, 6),
('FATIGUE',        'Have you felt unusual weakness or fatigue?',                                'additional', 1, 7),
('LOSS_APPETITE',  'Have you had a loss of appetite?',                                          'additional', 1, 8),
('CHILLS',         'Have you had chills?',                                                      'additional', 1, 9),
('TB_CONTACT',     'Have you been in close contact with someone diagnosed with TB?',            'risk_factor', 3, 10),
('HIV_STATUS',     'Are you HIV positive or immunocompromised?',                                'risk_factor', 2, 11),
('DIABETES',       'Do you have diabetes?',                                                     'risk_factor', 1, 12),
('SMOKING',        'Do you currently smoke or have a history of smoking?',                      'risk_factor', 1, 13)
ON DUPLICATE KEY UPDATE question_text = VALUES(question_text);

-- ------------------------------------------------------------
-- D3: Rules Database / TB Knowledge-Based System
-- Stores the if-then thresholds used by the rule engine
-- (kept in the DB, not hard-coded, so an admin/expert can tune them)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS rules (
    rule_id        INT AUTO_INCREMENT PRIMARY KEY,
    min_score      INT NOT NULL,
    max_score      INT NOT NULL,
    risk_level     ENUM('Negative','Low','Moderate','High') NOT NULL,
    tb_status      ENUM('Negative','Presumptive Positive') NOT NULL,
    advice         TEXT NOT NULL
) ENGINE=InnoDB;

INSERT INTO rules (min_score, max_score, risk_level, tb_status, advice) VALUES
(0,  3,  'Negative', 'Negative',
 'Your symptom profile does not currently suggest TB. Continue to monitor your health and seek care if symptoms develop or worsen.'),
(4,  8,  'Low', 'Presumptive Positive',
 'You show a low but present likelihood of TB. Please visit Bayara Hospital for a physical examination and, if advised, a sputum test.'),
(9,  14, 'Moderate', 'Presumptive Positive',
 'You show a moderate likelihood of TB based on the WHO four-symptom screen. Please visit Bayara Hospital promptly for sputum microscopy or GeneXpert testing.'),
(15, 100,'High', 'Presumptive Positive',
 'You show a high likelihood of TB. Please visit Bayara Hospital as soon as possible for confirmatory testing (sputum microscopy, GeneXpert, or chest radiograph) and clinical evaluation.')
ON DUPLICATE KEY UPDATE advice = VALUES(advice);

-- ------------------------------------------------------------
-- Diagnosis session + answers + result (what gets "stored for
-- future references" per pseudocode step 7 / Figure 4 D1)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS diagnoses (
    diagnosis_id   INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT NOT NULL,
    total_score    INT NOT NULL,
    tb_status      ENUM('Negative','Presumptive Positive') NOT NULL,
    risk_level     ENUM('Negative','Low','Moderate','High') NOT NULL,
    advice         TEXT NOT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS diagnosis_answers (
    answer_id      INT AUTO_INCREMENT PRIMARY KEY,
    diagnosis_id   INT NOT NULL,
    symptom_id     INT NOT NULL,
    answer         TINYINT(1) NOT NULL,  -- 1 = yes, 0 = no
    FOREIGN KEY (diagnosis_id) REFERENCES diagnoses(diagnosis_id) ON DELETE CASCADE,
    FOREIGN KEY (symptom_id)   REFERENCES symptoms(symptom_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Appointments (pseudocode step: "system gives appointment for
-- further treatment and checkup")
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT NOT NULL,
    diagnosis_id   INT DEFAULT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status         ENUM('Pending','Confirmed','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
    notes          VARCHAR(255),
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (diagnosis_id) REFERENCES diagnoses(diagnosis_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Contact Us / Enquiry (D4 in Figure 4)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS enquiries (
    enquiry_id     INT AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(100) NOT NULL,
    email          VARCHAR(120) NOT NULL,
    message        TEXT NOT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- NOTE: No admin account is seeded here because password hashes must be
-- generated by PHP's password_hash() at runtime, not hard-coded in SQL.
-- Run setup_admin.php once (see project root) to create the admin account.
