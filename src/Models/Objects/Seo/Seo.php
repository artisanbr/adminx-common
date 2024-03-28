<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Seo;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Objects\Seo\Config\SeoConfig;
use Illuminate\Support\Collection;
use ArtisanBR\GenericModel\GenericModel;


class Seo extends GenericModel
{

    protected $fillable = [
        'title',
        'title_prefix',
        'image_url',
        'description',
        'keywords',
        'robots',
        'config',
        'gtag',
        'html',
        'document_type',
        'canonical_uri',
        'published_at',
        'updated_at',
    ];

    protected $attributes = [
        'document_type' => 'page', // (page, article)
        'title'         => '',
        'title_prefix'  => null,
        'description'   => '',
        'keywords'      => '',
        'robots'        => 'index, follow',
        'config'        => [],
        'gtag'          => null,
        'canonical_uri' => null,
    ];

    protected $casts = [
        'title'          => 'string',
        'image_url'      => 'string',
        'description'    => 'string',
        'keywords'       => 'string',
        'keywords_array' => 'collection',
        'robots'         => 'string',
        'html'           => 'string',
        'document_type'  => 'string',
        'canonical_uri'  => 'string',
        'gtag'           => 'string',
        'config'         => SeoConfig::class,
        'published_at'   => 'string',
        'updated_at'     => 'string',
    ];

    protected $appends = [
        'keywords_array',
        //'image',
    ];

    public function mergeWith(self|array $seo): static
    {

        if (is_array($seo)) {
            $this->fill($seo);
        }
        else {
            $mergeAttrs = $seo->toArray();
            $mergeAttrs = collect($mergeAttrs)->filter(fn($item) => !empty($item))->toArray();
            $this->fill($mergeAttrs);
        }

        return $this;
    }


    //region ATTRIBUTES

    //region  SETS
    protected function setKeywordsAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['keywords'] = collect($value)->filter()->implode(',');
        }
        else {
            $this->attributes['keywords'] = (string)$value;
        }

        $this->attributes['keywords'] = Str::lower($this->attributes['keywords']);
    }
    //endregion

    //region GETS
    protected function getKeywordsAttribute(): string
    {
        return $this->attributes['keywords'] ?? '';
    }

    protected function getKeywordsArrayAttribute(): Collection
    {
        return collect(explode(',', $this->attributes['keywords']))->filter();
    }

    //endregion

    //endregion

}
