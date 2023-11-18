<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Assets\Resources;

use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\Objects\Frontend\Assets\Abstract\AbstractFrontendAssetResourcesObject;

class FrontendJsAssetsResources extends AbstractFrontendAssetResourcesObject
{

    public function __construct(array $attributes = [])
    {
        $this->addCasts([
                            'items' => AsCollectionOf::class . ':' . FrontendAssetsResourceJsScript::class,
                        ]);
        parent::__construct($attributes);
    }


    protected function getHtmlAttribute(): string
    {
        return ''; // $this->items->transform(fn($item) => Html::script($item))->join("\n");
    }
}
