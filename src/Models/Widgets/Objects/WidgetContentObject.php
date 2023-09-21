<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Widgets\Objects;


use Adminx\Common\Models\Objects\Abstract\AbstractHtmlObject;

class WidgetContentObject extends AbstractHtmlObject
{

    public function __construct(array $attributes = [])
    {
        $this->addCasts([
            'portal' => 'string'
                        ]);
        $this->addFillables([
            'portal'
                            ]);

        parent::__construct($attributes);
    }

    /*protected $fillable = [
        'html',
        'minify',
        //'elements',
        //'use_elements',
    ];

    protected $casts = [
        //'elements' => 'collection',
        //'use_elements' => 'bool',
        'html'   => 'string',
        'minify' => 'string',
    ];

    protected $attributes = [
        'html' => '',
    ];*/

    //region HELPERS
    /*public function builtHtml(Site $site, FrontendModel $model, $viewTemporaryName = 'element-html'): string
    {
        return AdvancedHtmlEngine::start($site, $model, $viewTemporaryName)->html($this->html)->buildHtml();
    }*/

}
