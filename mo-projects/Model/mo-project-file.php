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

}
