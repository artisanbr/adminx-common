<?php

namespace Adminx\Common\Models\Traits;

use Adminx\Common\Models\Generics\Seo;
use Adminx\Common\Models\Page;
use Adminx\Common\Models\Post;
use Adminx\Common\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @var Model|Site|Post|Page $this
 * @property Model|Site|Post|Page self
 * @property Model|Seo $seo
 */
trait HasSEO
{

    //region VALIDATION
    public static function extraRules(FormRequest $request = null): array
    {
        return [
            'seo.image_file' => [
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
        return ($prepend ?? false ? "{$prepend} - " : '') . ($this->seo->title ?? $this->title) . ($append ?? false ? " - {$append}" : '');
    }

    public function seoDescription($default = ''): string
    {
        return $this->seo->description ?? $default;
    }

    public function seoKeywords($default = ''): string
    {
        return $this->seo->keywords ?? $default;
    }

    public function seoImage(): string
    {

        if ($this->seo->image_id && $this->seo->image && $this->seo->image->url ?? false) {
            return $this->seo->image->url;
        }

        if ($this->cover && $this->cover->url ?? false) {
            return $this->cover->url;
        }

        //todo: Considerar imagens vinculadas

        return '';
    }
    //endregion

    //region SEO Helpers

    public function getTitle(): string
    {
        return $this->seoTitle();
    }

    public function getDescription(): string
    {
        return $this->seoDescription($this->site->seo->description ?? '');
    }

    public function getKeywords(): string
    {
        return $this->seoKeywords($this->site->seo->keywords ?? '');
    }

    public function getRobots(): string
    {
        return $this->seo->robots ?? $this->site->seo->robots ?? 'noindex, nofollow';
    }

    //endregion

    ////region RELATIONS
    //endregion

}
