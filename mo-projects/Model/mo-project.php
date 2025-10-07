<?php

namespace FacturaScripts\Plugins\MoProjects\Model;

use FacturaScripts\Core\Model\Cliente;
use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Where;

class MoProject extends MoModel
{
    public $id;
    public $code;
    public $name;
    public $idclient;
    public $idcompany;
    public $status;
    public $startdate;
    public $enddate;
    public $description;
    public $drive_folder_url;
    public $calendar_id;
    public $created_at;
    public $updated_at;

    public function clear(): void
    {
        parent::clear();
        $this->status = 'activo';
        $this->created_at = Tools::dateTime();
        $this->idcompany = (int)Tools::settings('default', 'idempresa');
    }

    public function primaryDescriptionColumn(): string
    {
        return 'name';
    }

    public static function tableName(): string
    {
        return 'mo_projects';
    }

    public function test(): bool
    {
        $this->code = strtoupper(Tools::noHtml($this->code));
        $this->name = Tools::noHtml($this->name);
        $this->description = Tools::noHtml($this->description);
        $this->drive_folder_url = Tools::noHtml($this->drive_folder_url);
        $this->calendar_id = Tools::noHtml($this->calendar_id);

        if (empty($this->code) || !preg_match('/^[A-Z0-9\-\_]{3,32}$/', $this->code)) {
            Tools::log()->warning('mo-projects-invalid-code');
            return false;
        }

        if (empty($this->name)) {
            Tools::log()->warning('mo-projects-invalid-name');
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
CREATE TABLE `mo_projects` (
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
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NULL,
    CONSTRAINT `fk_mo_projects_clients` FOREIGN KEY (`idclient`) REFERENCES `clientes` (`idcliente`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
    }

    public function getCredentials(): array
    {
        return MoProjectCredential::all([Where::eq('idproject', $this->id)]);
    }

    public function getDocuments(): array
    {
        return MoProjectDocument::all([Where::eq('idproject', $this->id)]);
    }

    public function getFiles(): array
    {
        return MoProjectFile::all([Where::eq('idproject', $this->id)]);
    }

    public function getTaskBoard(): array
    {
        return MoProjectTask::all([Where::eq('idproject', $this->id)], ['position' => 'ASC']);
    }

    public function getEvents(): array
    {
        return MoProjectEvent::all([Where::eq('idproject', $this->id)], ['start_at' => 'DESC']);
    }

    public function getCustomerName(): string
    {
        if (empty($this->idclient)) {
            return '';
        }

        $cliente = new Cliente();
        return $cliente->load($this->idclient) ? $cliente->nombre : '';
    }
}
