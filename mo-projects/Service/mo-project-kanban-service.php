<?php

namespace FacturaScripts\Plugins\MoProjects\Service;

use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Where;
use FacturaScripts\Plugins\MoProjects\Model\MoProjectTask;
use FacturaScripts\Plugins\MoProjects\Model\MoProjectTaskStatus;

class MoProjectKanbanService
{
    private static ?self $instance = null;

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function ensureDefaultColumns(int $idproject): void
    {
        $columns = MoProjectTaskStatus::all([Where::eq('idproject', $idproject)]);
        if (!empty($columns)) {
            return;
        }

        foreach (MoProjectTaskStatus::getDefaultColumns($idproject) as $column) {
            $column->save();
        }
    }

    public function getBoard(int $idproject): array
    {
        $columns = MoProjectTaskStatus::all([Where::eq('idproject', $idproject)], ['position' => 'ASC']);
        $tasks = MoProjectTask::byProject($idproject);

        $board = [];
        foreach ($columns as $column) {
            $board[$column->id] = [
                'column' => $column,
                'tasks' => [],
            ];
        }

        foreach ($tasks as $task) {
            if (!isset($board[$task->idstatus])) {
                $board[$task->idstatus] = [
                    'column' => null,
                    'tasks' => [],
                ];
            }
            $board[$task->idstatus]['tasks'][] = $task;
        }

        return $board;
    }

    public function moveTask(MoProjectTask $task, int $newStatusId, int $newPosition): bool
    {
        $task->idstatus = $newStatusId;
        $task->position = $newPosition;
        if (false === $task->save()) {
            return false;
        }

        Tools::log()->notice('mo-projects-task-moved', ['%task%' => $task->title]);
        return true;
    }
}
