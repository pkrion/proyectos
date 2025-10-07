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

}
