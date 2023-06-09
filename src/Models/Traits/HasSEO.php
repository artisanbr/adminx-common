<?php

namespace Adminx\Common\Models\Traits;

use Adminx\Common\Models\Objects\Seo\Seo;
use Adminx\Common\Models\Pages\Page;
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

        if (!empty($this->seo->image_url)) {
            return $this->seo->image_url;
        }

        if ($this->cover_url ?? false) {
            return $this->cover_url;
        }

        //todo: Considerar imagens vinculadas

        return '';
    }
    //endregion

    //region SEO Helpers

    public function getTitle(): string
    {
        return $this->seo->title ?? $this->title ?? '';
    }

    public function getDescription(): string
    {

        if(get_class($this) !== Site::class && ($this->site ?? false) && $this->site->seo->config->use_defaults){
            return $this->seo->description ?? $this->site->getDescription();
        }

        return $this->seo->description ?? '';
    }

    public function getKeywords(): string
    {

        if(get_class($this) !== Site::class && ($this->site ?? false) && $this->site->seo->config->use_defaults){
            return $this->seo->keywords ?? $this->site->getKeywords();
        }

        return $this->seo->keywords ?? '';

        //return $this->seoKeywords($this->site->seo->keywords ?? '');
    }

    public function getRobots(): string
    {
        if(get_class($this) !== Site::class && ($this->site ?? false) && $this->site->seo->config->use_defaults){
            return $this->seo->robots ?? $this->site->getRobots();
        }

        return $this->seo->robots ?? 'noindex, nofollow';
    }

    public function getGTagScript(): string
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

    //endregion

    ////region RELATIONS
    //endregion

}
