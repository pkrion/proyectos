<?php

namespace FacturaScripts\Plugins\MoProjects\Model;

use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Where;

class MoProjectTask extends MoModel
{
    public $id;
    public $idproject;
    public $idstatus;
    public $title;
    public $description;
    public $assigned_to;
    public $due_date;
    public $priority;
    public $position;
    public $created_at;
    public $updated_at;

    public static function tableName(): string
    {
        return 'mo_project_tasks';
    }

    public function test(): bool
    {
        $this->title = Tools::noHtml($this->title);
        $this->description = Tools::noHtml($this->description);
        $this->priority = $this->priority ?: 'normal';
        $this->due_date = empty($this->due_date) ? null : Tools::date($this->due_date);
        $this->position = (int)$this->position;

        if (empty($this->idproject) || empty($this->idstatus) || empty($this->title)) {
            Tools::log()->warning('mo-projects-invalid-task');
            return false;
        }

        if (!in_array($this->priority, ['low', 'normal', 'high'], true)) {
            $this->priority = 'normal';
        }

        if (!parent::test()) {
            return false;
        }

        if ($this->id) {
            $this->updated_at = Tools::dateTime();
        }

        return true;
    }

    public static function byProject(int $idproject): array
    {
        return self::all([Where::eq('idproject', $idproject)], ['position' => 'ASC']);
    }
}
