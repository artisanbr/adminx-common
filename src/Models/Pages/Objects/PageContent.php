<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Pages\Objects;

use Adminx\Common\Libs\Helpers\HtmlHelper;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Objects\Abstract\AbstractHtmlObject;
use Illuminate\Support\Collection;

class PageContent extends AbstractHtmlObject
{

    protected $fillable = [
        //'main',
        //'internal',
        'html',
    ];

    protected $casts = [
        //'main'        => FrontendHtmlObject::class,
        //'internal'    => FrontendHtmlObject::class,
        'html'        => 'string',
        'plain_text'  => 'string',
        'short_text'  => 'string',
        'description' => 'string',
    ];

    protected $temporary = ['main','internal'];

    protected $attributes = [
        //'main' => [],
        //'internal' => [],
    ];

    protected function getPlainTextAttribute(): string
    {
        return HtmlHelper::removeAllTags($this->html);
    }

    protected function getShortTextAttribute(): string
    {
        return $this->textLimit();
    }

    protected function getDescriptionAttribute(): string
    {
        return $this->textLimit();
    }

    protected function getKeywordsAttribute(): array
    {
        return $this->topWords()->toArray();
    }

    public function textLimit($limit = 300): string
    {
        return Str::limit($this->plain_text, $limit);
    }

    public function topWords($words = 40): Collection
    {
        return collect(Str::mostFrequentWords($this->plain_text, $words))->keys();
    }
}
