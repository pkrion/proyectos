<?php

namespace FacturaScripts\Plugins\MoProjects\Model;

use FacturaScripts\Core\Tools;

class MoProjectCredential extends MoModel
{
    public $id;
    public $idproject;
    public $title;
    public $username;
    public $password;
    public $notes;
    public $is_sensitive;
    public $created_at;
    public $updated_at;

    public static function tableName(): string
    {
        return 'mo_project_credentials';
    }

    public function test(): bool
    {
        $this->title = Tools::noHtml($this->title);
        $this->username = Tools::noHtml($this->username);
        $this->password = Tools::noHtml($this->password);
        $this->notes = Tools::noHtml($this->notes);
        $this->is_sensitive = (bool)$this->is_sensitive;

        if (empty($this->idproject)) {
            Tools::log()->warning('mo-projects-missing-project');
            return false;
        }

        if (empty($this->title)) {
            Tools::log()->warning('mo-projects-missing-credential-title');
            return false;
        }

        if (!parent::test()) {
            return false;
        }

        if ($this->id) {
            $this->updated_at = Tools::dateTime();
        }

        return true;
    }

    public function install(): string
    {
        return <<<SQL
CREATE TABLE `mo_project_credentials` (
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
SQL;
    }

    public function getProject(): ?MoProject
    {
        $project = new MoProject();
        return $project->load($this->idproject) ? $project : null;
    }
}
