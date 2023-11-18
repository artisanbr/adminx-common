<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Assets\Resources;

use Adminx\Common\Models\Objects\Frontend\Assets\Abstract\AbstractFrontendAssetsResourceScript;

class FrontendAssetsResourceCssScript extends AbstractFrontendAssetsResourceScript
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    //region Attributes
    //region GET's
    //protected function getAttribute(){return $this->attributes[""];}

    protected function getHtmlAttribute()
    {

    }

    //endregion

    //region SET's
    //protected function setAttribute($value){}

    //endregion
    //endregion
}
