<?php
/**
 * Controlador mo-projects: listado y alta de proyectos.
 */

namespace FacturaScripts\Plugins\MoProjects\Controller;

use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Core\Lib\ExtendedController\BaseView;
use FacturaScripts\Core\Model\Cliente;
use FacturaScripts\Plugins\MoProjects\Model\MoProject;
use FacturaScripts\Plugins\MoProjects\Service\MoProjectDocumentService;
use FacturaScripts\Plugins\MoProjects\Service\MoProjectKanbanService;

class MoProjects extends Controller
{
    protected function createViews(): void
    {
        $this->addView('ListMoProjects', 'MoProject', 'mo-projects', 'fas fa-diagram-project');
        $this->addSearchFields('ListMoProjects', ['code', 'name', 'status']);
        $this->addOrderBy('ListMoProjects', ['startdate', 'enddate', 'name'], 'startdate', 2);

        $this->addView('EditMoProject', 'MoProject', 'mo-project', 'fas fa-folder-open');
        $this->setTabs('EditMoProject', [
            'general' => 'general',
            'documentos' => 'documentos',
            'credenciales' => 'credenciales',
            'archivos' => 'archivos',
            'kanban' => 'kanban',
            'calendario' => 'calendario',
        ]);
    }

    protected function customizeView(string $viewName, BaseView $view): void
    {
        switch ($viewName) {
            case 'ListMoProjects':
                $view->addButton('btn-add-project', [
                    'label' => 'nuevo proyecto',
                    'icon' => 'fas fa-plus',
                    'class' => 'btn-success',
                    'route' => 'EditMoProject',
                    'action' => 'new',
                ]);
                break;
            case 'EditMoProject':
                $model = $view->getModel();
                if ($model instanceof MoProject && $model->id > 0) {
                    $view->assign('documents', MoProjectDocumentService::getInstance()->getProjectDocuments($model));
                    $view->assign('kanbanColumns', MoProjectKanbanService::getInstance()->getBoard($model->id));
                }
                $view->assign('clientes', Cliente::all());
                break;
        }
    }

    protected function execAfterAction(string $action): void
    {
        if ('save' === $action) {
            /** @var MoProject $project */
            $project = $this->getViewModel('EditMoProject');
            if ($project instanceof MoProject) {
                MoProjectKanbanService::getInstance()->ensureDefaultColumns($project->id);
            }
        }
    }

    protected function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'mo-projects';
        $data['icon'] = 'fas fa-diagram-project';
        return $data;
    }

    protected function loadData(string $viewName, array $params = []): void
    {
        if ('EditMoProject' === $viewName && !empty($params['id'])) {
            /** @var MoProject|null $project */
            $project = $this->views[$viewName]->getModel();
            if ($project instanceof MoProject && $project->loadFromCode($params['id'])) {
                $this->setTemplate('mo-projects/edit');
                return;
            }
        }

        parent::loadData($viewName, $params);
    }
}
