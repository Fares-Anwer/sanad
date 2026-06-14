<?php
require_once __DIR__ . '/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `full_name`     VARCHAR(100)  NOT NULL,
        `phone`         VARCHAR(20)   NOT NULL UNIQUE,
        `email`         VARCHAR(150)  NOT NULL UNIQUE,
        `password_hash` VARCHAR(255)  NOT NULL,
        `role`          ENUM('beneficiary', 'donor', 'admin') NOT NULL DEFAULT 'beneficiary',
        `governorate`   VARCHAR(50)   NOT NULL,
        `district`      VARCHAR(100)  NOT NULL,
        `is_active`     TINYINT(1)    NOT NULL DEFAULT 1,
        `created_at`    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `devices` (
        `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `donor_id`          INT UNSIGNED NOT NULL,
        `name`              VARCHAR(150) NOT NULL,
        `category`          ENUM('respiratory','mobility','beds_clinical','diagnostic') NOT NULL,
        `condition_rating`  ENUM('excellent','good','acceptable') NOT NULL,
        `description`       TEXT NOT NULL,
        `offer_type`        ENUM('donation','loan') NOT NULL,
        `loan_duration`     VARCHAR(50)  DEFAULT NULL,
        `governorate`       VARCHAR(50)  NOT NULL,
        `district`          VARCHAR(100) NOT NULL,
        `latitude`          DECIMAL(10,8) DEFAULT NULL,
        `longitude`         DECIMAL(11,8) DEFAULT NULL,
        `status`            ENUM('pending_review','active','under_request_review','loaned','rejected') NOT NULL DEFAULT 'pending_review',
        `rejection_reason`  TEXT DEFAULT NULL,
        `admin_reviewed_by` INT UNSIGNED DEFAULT NULL,
        `admin_reviewed_at` DATETIME DEFAULT NULL,
        `created_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`donor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`admin_reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `device_photos` (
        `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `device_id`   INT UNSIGNED NOT NULL,
        `file_path`   VARCHAR(255) NOT NULL,
        `is_primary`  TINYINT(1)   NOT NULL DEFAULT 0,
        `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`device_id`) REFERENCES `devices`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `requests` (
        `id`                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `device_id`             INT UNSIGNED NOT NULL,
        `beneficiary_id`        INT UNSIGNED NOT NULL,
        `case_description`      TEXT NOT NULL,
        `medical_doc_path`      VARCHAR(255) NOT NULL,
        `status`                ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
        `rejection_reason`      TEXT DEFAULT NULL,
        `admin_reviewed_by`     INT UNSIGNED DEFAULT NULL,
        `admin_reviewed_at`     DATETIME DEFAULT NULL,
        `created_at`            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`device_id`)         REFERENCES `devices`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`beneficiary_id`)    REFERENCES `users`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`admin_reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
