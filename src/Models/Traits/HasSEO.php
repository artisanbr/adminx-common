<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits;

use Adminx\Common\Models\Article;
use Adminx\Common\Models\Objects\Seo\Seo;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Sites\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @var Model|Site|Article|Page      $this
 * @property Model|Site|Article|Page self
 * @property Model|Seo               $seo
 */
trait HasSEO
{

    //region VALIDATION
    public static function extraRules(FormRequest $request = null): array
    {
        return [
            'seo.image_file' => [
                'nullable',
                'image',
                'mimes:' . collect(config('adminx.defines.files.types.image'))->implode(','),
                'max:1536',
            ],
        ];
    }

    public static function extraMessages(FormRequest $request = null): array
    {
        return [
            'seo.image_file.image' => 'O arquivo precisa ser uma imagem',
            'seo.image_file.mimes' => 'Este formato não é aceito, utilize .jpg, .png, .jpeg, .gif ou .svg',
            'seo.image_file.max'   => 'Este arquivo ultrapassa o limite de 1,5Mb',
        ];
    }
    //endregion

    //region HELPERS
    public function seoTitle($append = null, $prepend = null): string
    {

        $title = blank($this->seo->title) ? $this->title : $this->seo->title;

        return ($prepend ?? false ? "{$prepend} - " : '') . $title . ($append ?? false ? " - {$append}" : '');
    }

    public function seoDescription($default = null): string
    {
        if(get_class($this) !== Site::class && ($this->site ?? false) && $this->site->seo->config->use_defaults && !$default){
            $default = $this->site->seoDescription();
        }

        return $this->seo->description ?? @$this->description ?? $default;
    }

    public function seoKeywords($default = ''): string
    {
        return $this->seo->keywords ?? @$this->keywords ?? $default;
    }

    public function seoImage(?string $default = null): ?string
    {


        if ($this->attributes['cover_url'] ?? false) {
            return $this->attributes['cover_url'];
        }

        if ($this->cover_url ?? false) {
            return $this->cover_url;
        }

        if (!empty($this->seo->image_url ?? null)) {
            return $this->seo->image_url;
        }


        if ($this->image_url ?? false) {
            return $this->image_url;
        }

        //todo: Considerar imagens vinculadas

        return $default;
    }
    //endregion

    //region SEO Helpers

    /*public function seoTitle(): string
    {
        return $this->seo->title ?? $this->title ?? '';
    }

    public function getDescription(): string
    {

        if(get_class($this) !== Site::class && ($this->site ?? false) && $this->site->seo->config->use_defaults){
            return $this->seo->description ?? $this->site->getDescription();
        }

        return $this->seoDescription();
    }

    public function getKeywords(): string
    {

        if(get_class($this) !== Site::class && ($this->site ?? false) && $this->site->seo->config->use_defaults){
            return $this->seoKeywords() ?? $this->site->getKeywords();
        }

        return $this->seoKeywords();

        //return $this->seoKeywords($this->site->seo->keywords ?? '');
    }*/

    public function getRobots(): string
    {
        if(get_class($this) !== Site::class && ($this->site ?? false) && $this->site->seo->config->use_defaults){
            return $this->seo->robots ?? $this->site->getRobots();
        }

        return $this->seo->robots ?? 'noindex, nofollow';
    }

    public function getGTagHeadScript(): string
    {
        $gtag = $this->seo->gtag ?? $this->site->seo->gtag ?? false;

        return !$gtag ? '' : <<<html
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$gtag}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '{$gtag}');
</script>
html;

    }

    public function getGTagBodyScript(): string
    {
        $gtag = $this->seo->gtag ?? $this->site->seo->gtag ?? false;

        return !$gtag ? '' : <<<html
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$gtag}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
html;

    }

    //endregion

    ////region RELATIONS
    //endregion

}
