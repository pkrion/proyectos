<?php

namespace FacturaScripts\Plugins\MoProjects\Model;

use FacturaScripts\Core\Tools;

class MoProjectDocument extends MoModel
{
    public $id;
    public $idproject;
    public $doctype;
    public $iddocument;
    public $summary;
    public $created_at;

    public static function tableName(): string
    {
        return 'mo_project_documents';
    }

    public function test(): bool
    {
        $this->doctype = Tools::noHtml($this->doctype);
        $this->summary = Tools::noHtml($this->summary);

        if (empty($this->idproject) || empty($this->iddocument)) {
            Tools::log()->warning('mo-projects-missing-document');
            return false;
        }

        if (!in_array($this->doctype, ['factura', 'albaran', 'presupuesto'], true)) {
            Tools::log()->warning('mo-projects-invalid-document-type');
            return false;
        }

        return parent::test();
    }

    public function install(): string
    {
        return <<<SQL
CREATE TABLE `mo_project_documents` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `idproject` INT UNSIGNED NOT NULL,
    `doctype` ENUM('factura','albaran','presupuesto') NOT NULL,
    `iddocument` INT UNSIGNED NOT NULL,
    `summary` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_mo_project_documents_project` FOREIGN KEY (`idproject`) REFERENCES `mo_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
    }
}
