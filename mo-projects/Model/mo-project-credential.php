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

    public function getProject(): ?MoProject
    {
        $project = new MoProject();
        return $project->load($this->idproject) ? $project : null;
    }
}
