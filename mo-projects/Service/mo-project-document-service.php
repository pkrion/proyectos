<?php

namespace FacturaScripts\Plugins\MoProjects\Service;

use FacturaScripts\Core\Model\AlbaranCliente;
use FacturaScripts\Core\Model\FacturaCliente;
use FacturaScripts\Core\Model\PresupuestoCliente;
use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Where;
use FacturaScripts\Plugins\MoProjects\Model\MoProject;
use FacturaScripts\Plugins\MoProjects\Model\MoProjectDocument;

class MoProjectDocumentService
{
    private static ?self $instance = null;

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getProjectDocuments(MoProject $project): array
    {
        $documents = MoProjectDocument::all([Where::eq('idproject', $project->id)], ['created_at' => 'DESC']);
        $result = [];
        foreach ($documents as $link) {
            $result[] = $this->decorateLink($link);
        }

        return $result;
    }

    public function linkDocument(MoProject $project, string $doctype, int $iddocument, string $summary = ''): bool
    {
        $link = new MoProjectDocument([
            'idproject' => $project->id,
            'doctype' => $doctype,
            'iddocument' => $iddocument,
            'summary' => $summary,
        ]);

        if (false === $link->save()) {
            return false;
        }

        Tools::log()->notice('mo-projects-document-linked', ['%code%' => $project->code]);
        return true;
    }

    protected function decorateLink(MoProjectDocument $link): array
    {
        $data = [
            'link' => $link,
            'document' => null,
            'url' => '',
        ];

        switch ($link->doctype) {
            case 'factura':
                $doc = new FacturaCliente();
                if ($doc->load($link->iddocument)) {
                    $data['document'] = $doc;
                    $data['url'] = $doc->url('edit');
                }
                break;
            case 'albaran':
                $doc = new AlbaranCliente();
                if ($doc->load($link->iddocument)) {
                    $data['document'] = $doc;
                    $data['url'] = $doc->url('edit');
                }
                break;
            case 'presupuesto':
                $doc = new PresupuestoCliente();
                if ($doc->load($link->iddocument)) {
                    $data['document'] = $doc;
                    $data['url'] = $doc->url('edit');
                }
                break;
        }

        return $data;
    }
}
