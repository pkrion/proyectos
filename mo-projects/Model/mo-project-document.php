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

}
