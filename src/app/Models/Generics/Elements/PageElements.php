<?php
namespace ArtisanBR\Adminx\Common\App\Models\Generics\Elements;



use ArtisanLabs\GModel\GenericModel;

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
