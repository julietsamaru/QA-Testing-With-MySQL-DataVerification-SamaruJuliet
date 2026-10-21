-- Supplemental local schema for this QA exercise. No existing rows are deleted.
CREATE DATABASE IF NOT EXISTS birthday_wishnote CHARACTER SET utf8mb4;
USE birthday_wishnote;
CREATE TABLE IF NOT EXISTS main_corrupt_politicians (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(30) NOT NULL,
    LastName VARCHAR(30) NOT NULL,
    GovPosition VARCHAR(20) NOT NULL,
    Location VARCHAR(40) NULL DEFAULT NULL
) CHARACTER SET utf8mb4;
-- Fictional baseline, inserted only when this table is empty.
INSERT INTO main_corrupt_politicians (FirstName, LastName, GovPosition, Location)
SELECT 'Baseline', 'QARecord', 'Test Position', 'Test Location'
WHERE NOT EXISTS (SELECT 1 FROM main_corrupt_politicians);
