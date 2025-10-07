-- Tablas para mo-projects

CREATE TABLE IF NOT EXISTS `mo_projects` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(32) NOT NULL UNIQUE,
    `name` VARCHAR(255) NOT NULL,
    `idclient` INT UNSIGNED NULL,
    `idcompany` INT UNSIGNED NOT NULL,
    `status` VARCHAR(32) NOT NULL DEFAULT 'activo',
    `startdate` DATE NULL,
    `enddate` DATE NULL,
    `description` TEXT NULL,
    `drive_folder_url` VARCHAR(255) NULL,
    `calendar_id` VARCHAR(128) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL,
    CONSTRAINT `fk_mo_projects_clients` FOREIGN KEY (`idclient`) REFERENCES `clientes` (`idcliente`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `mo_project_documents` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `idproject` INT UNSIGNED NOT NULL,
    `doctype` ENUM('factura','albaran','presupuesto') NOT NULL,
    `iddocument` INT UNSIGNED NOT NULL,
    `summary` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_mo_project_documents_project` FOREIGN KEY (`idproject`) REFERENCES `mo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `mo_project_credentials` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `idproject` INT UNSIGNED NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `username` VARCHAR(100) NULL,
    `password` VARCHAR(100) NULL,
    `notes` TEXT NULL,
    `is_sensitive` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL,
    CONSTRAINT `fk_mo_project_credentials_project` FOREIGN KEY (`idproject`) REFERENCES `mo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `mo_project_files` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `idproject` INT UNSIGNED NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `filepath` VARCHAR(255) NOT NULL,
    `filesize` INT UNSIGNED NOT NULL DEFAULT 0,
    `mimetype` VARCHAR(100) NULL,
    `uploaded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_mo_project_files_project` FOREIGN KEY (`idproject`) REFERENCES `mo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `mo_project_tasks` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `idproject` INT UNSIGNED NOT NULL,
    `idstatus` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `assigned_to` INT UNSIGNED NULL,
    `due_date` DATE NULL,
    `priority` ENUM('low','normal','high') NOT NULL DEFAULT 'normal',
    `position` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL,
    CONSTRAINT `fk_mo_project_tasks_project` FOREIGN KEY (`idproject`) REFERENCES `mo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `mo_project_task_statuses` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `idproject` INT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `color` VARCHAR(7) NOT NULL DEFAULT '#3b82f6',
    `position` INT UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT `fk_mo_project_task_statuses_project` FOREIGN KEY (`idproject`) REFERENCES `mo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `mo_project_events` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `idproject` INT UNSIGNED NOT NULL,
    `calendar_event_id` VARCHAR(128) NULL,
    `title` VARCHAR(255) NOT NULL,
    `start_at` DATETIME NOT NULL,
    `end_at` DATETIME NULL,
    `location` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `synced` TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT `fk_mo_project_events_project` FOREIGN KEY (`idproject`) REFERENCES `mo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
