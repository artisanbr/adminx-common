<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Assets;

use Adminx\Common\Models\Objects\Frontend\Assets\Code\FrontendJsAssetsCode;
use ArtisanLabs\GModel\GenericModel;

class FrontendJsAssetsBundle extends GenericModel
{

    protected $fillable = [
        'head',
        'before_body',
        'after_body',
    ];

    protected $casts = [
        'head' => FrontendJsAssetsCode::class,
        'before_body' => FrontendJsAssetsCode::class,
        'after_body' => FrontendJsAssetsCode::class,
    ];

    /*protected $attributes = [
        'head' => [],
        'before_body' => [],
        'after_body' => [],
    ];*/

    public function minify(){
        $this->head->minify();
        $this->before_body->minify();
        $this->after_body->minify();

        return $this;
    }
}
