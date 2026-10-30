-- Safe to run on a new OR an existing database:   sudo mysql < schema.sql
CREATE DATABASE IF NOT EXISTS myedvt_symposium
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE myedvt_symposium;

CREATE TABLE IF NOT EXISTS registrations (
  id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name          VARCHAR(150) NOT NULL,
  national_id        VARCHAR(20)  NOT NULL,
  age                TINYINT UNSIGNED NOT NULL,
  gender             ENUM('Male','Female') NOT NULL,
  email              VARCHAR(150) NULL,
  phone              VARCHAR(20)  NOT NULL,
  employment_status  ENUM('Employed','Unemployed','Student','Self-employed') NOT NULL,
  province           ENUM('Harare','Mashonaland East','Mashonaland West','Mashonaland Central',
                          'Matabeleland South','Matabeleland North','Manicaland','Midlands',
                          'Bulawayo','Masvingo') NOT NULL,
  heard_about_us     ENUM('Social media (other than Ministry of Youth platforms)',
                          'Friends or family','An organisation',
                          'Ministry of Youth platforms (e.g., Facebook or X)') NOT NULL,
  consent_at         TIMESTAMP NULL,
  created_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_national_id (national_id),
  KEY idx_province (province),
  KEY idx_employment (employment_status),
  KEY idx_created (created_at)
) ENGINE=InnoDB;

-- For databases created earlier without the consent column (does nothing if it already exists)
ALTER TABLE registrations ADD COLUMN IF NOT EXISTS consent_at TIMESTAMP NULL AFTER heard_about_us;
