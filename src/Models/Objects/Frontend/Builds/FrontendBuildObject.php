<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Builds;

use Adminx\Common\Models\Objects\Frontend\Builds\Common\FrontendBuildBodyObject;
use Adminx\Common\Models\Objects\Frontend\Builds\Common\FrontendBuildHeadObject;
use Adminx\Common\Models\Objects\Seo\Seo;
use ArtisanBR\GenericModel\Model as GenericModel;
use Butschster\Head\Contracts\Packages\ManagerInterface;
use Butschster\Head\MetaTags\Meta;

/**
 * @property string $html
 * @property string $minify
 */
class FrontendBuildObject extends GenericModel
{

    protected $fillable = [
        //'meta',
        'lang',
        'head',
        'body',
        'seo',
    ];

    protected $casts = [
        'lang' => 'string',
        'head' => FrontendBuildHeadObject::class,
        'body' => FrontendBuildBodyObject::class,
        'seo'  => Seo::class,
        //'meta' => null,
    ];

    protected $attributes = [
        /*body' => [],*/
    ];

    protected ?Meta $metaCache = null;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    protected function getLangAttribute()
    {
        return $this->attributes['lang'] ?? str_replace('_', '-', app()->getLocale());
    }

    protected function getMetaAttribute()
    {

        if (!$this->metaCache) {
            $this->metaCache = new Meta(
                app(ManagerInterface::class),
                app('config')
            );

            //$this->metaCache->addCsrfToken();
            $this->metaCache->initialize();
            $this->metaCache->removeTag('csrf-token');
            //$this->metaCache->reset();
        }


        return $this->metaCache;
    }

}
