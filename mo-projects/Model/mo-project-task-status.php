<?php

namespace FacturaScripts\Plugins\MoProjects\Model;

use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Where;

class MoProjectTaskStatus extends MoModel
{
    public $id;
    public $idproject;
    public $name;
    public $color;
    public $position;

    public static function tableName(): string
    {
        return 'mo_project_task_statuses';
    }

    public function test(): bool
    {
        $this->name = Tools::noHtml($this->name);
        $this->color = Tools::noHtml($this->color ?: '#3b82f6');
        $this->position = (int)$this->position;

        if (empty($this->idproject) || empty($this->name)) {
            Tools::log()->warning('mo-projects-invalid-status');
            return false;
        }

        if (!preg_match('/^#([0-9a-fA-F]{3}){1,2}$/', $this->color)) {
            $this->color = '#3b82f6';
        }

        if (!parent::test()) {
            return false;
        }

        return true;
    }

    public static function getDefaultColumns(int $idproject): array
    {
        $columns = self::all([Where::eq('idproject', $idproject)], ['position' => 'ASC']);
        if ($columns) {
            return $columns;
        }

        return [
            new self(['idproject' => $idproject, 'name' => 'Backlog', 'color' => '#9ca3af', 'position' => 10]),
            new self(['idproject' => $idproject, 'name' => 'En progreso', 'color' => '#3b82f6', 'position' => 20]),
            new self(['idproject' => $idproject, 'name' => 'Revisión', 'color' => '#f97316', 'position' => 30]),
            new self(['idproject' => $idproject, 'name' => 'Finalizado', 'color' => '#22c55e', 'position' => 40]),
        ];
    }
}
