<?php

namespace FacturaScripts\Plugins\MoProjects\Model;

use FacturaScripts\Core\Template\ModelClass;
use FacturaScripts\Core\Template\ModelTrait;

abstract class MoModel extends ModelClass
{
    use ModelTrait;

    public static function primaryColumn(): string
    {
        return 'id';
    }

    public function clear(): void
    {
        parent::clear();
    }
}
