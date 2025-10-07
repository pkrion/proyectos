<?php

namespace FacturaScripts\Plugins\MoProjects\Model;

use FacturaScripts\Core\Tools;

class MoProjectFile extends MoModel
{
    public $id;
    public $idproject;
    public $filename;
    public $filepath;
    public $filesize;
    public $mimetype;
    public $uploaded_at;

    public static function tableName(): string
    {
        return 'mo_project_files';
    }

    public function test(): bool
    {
        $this->filename = Tools::noHtml($this->filename);
        $this->filepath = Tools::noHtml($this->filepath);
        $this->mimetype = Tools::noHtml($this->mimetype);

        if (empty($this->idproject) || empty($this->filename) || empty($this->filepath)) {
            Tools::log()->warning('mo-projects-invalid-file');
            return false;
        }

        if (!parent::test()) {
            return false;
        }

        return true;
    }

    public function install(): string
    {
        return <<<SQL
CREATE TABLE `mo_project_files` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `idproject` INT UNSIGNED NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `filepath` VARCHAR(255) NOT NULL,
    `filesize` INT UNSIGNED NOT NULL DEFAULT 0,
    `mimetype` VARCHAR(100) NULL,
    `uploaded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_mo_project_files_project` FOREIGN KEY (`idproject`) REFERENCES `mo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
    }
}
