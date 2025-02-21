<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\Values;

use Adminx\Common\Objects\Files\ImageFileObject;

class ImageValue extends ImageFileObject
{

    public function __construct(array $attributes = [])
    {
        $this->addFillables([
                                'title',
                                'description',
                            ]);
        $this->addCasts([
                            'title'       => 'string',
                            'description' => 'string',
                        ]);
        parent::__construct($attributes);
    }

}
