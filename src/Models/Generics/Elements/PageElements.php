<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Elements;
use ArtisanBR\GenericModel\GenericModel;



class PageElements extends GenericModel
{

    protected $fillable = [
        'content',
        'internal_content',
    ];

    protected $casts = [
        'content' => HtmlElement::class,
        'internal_content' => HtmlElement::class,
    ];
}
