<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Builds\Common;

use Adminx\Common\Models\Objects\Frontend\Builds\Common\Abstract\AbstractFrontendBuildSectionObject;

/**
 * @property string $html
 * @property string $minify
 */
class FrontendBuildBodyObject extends AbstractFrontendBuildSectionObject
{

    public function __construct(array $attributes = [])
    {

        $this->addFillables([
                                'gtag_script',
                                'class',
                                'id',
                            ]);

        $this->addCasts([
                            'gtag_script' => 'string',
                            'class' => 'string',
                            'id'    => 'string',
                        ]);

        parent::__construct($attributes);
    }


}
