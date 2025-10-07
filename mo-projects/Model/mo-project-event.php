<?php

namespace FacturaScripts\Plugins\MoProjects\Model;

use FacturaScripts\Core\Tools;

class MoProjectEvent extends MoModel
{
    public $id;
    public $idproject;
    public $calendar_event_id;
    public $title;
    public $start_at;
    public $end_at;
    public $location;
    public $notes;
    public $synced;

    public static function tableName(): string
    {
        return 'mo_project_events';
    }

    public function test(): bool
    {
        $this->title = Tools::noHtml($this->title);
        $this->location = Tools::noHtml($this->location);
        $this->notes = Tools::noHtml($this->notes);
        $this->synced = (bool)$this->synced;
        $this->calendar_event_id = Tools::noHtml($this->calendar_event_id);
        $this->start_at = Tools::dateTime($this->start_at);
        $this->end_at = empty($this->end_at) ? null : Tools::dateTime($this->end_at);

        if (empty($this->idproject) || empty($this->title) || empty($this->start_at)) {
            Tools::log()->warning('mo-projects-invalid-event');
            return false;
        }

        return parent::test();
    }

    public function install(): string
    {
        return <<<SQL
CREATE TABLE `mo_project_events` (
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
SQL;
    }
}
