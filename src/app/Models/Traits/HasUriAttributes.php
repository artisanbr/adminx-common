<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits;

use ArtisanBR\Adminx\Common\App\Libs\Support\Str;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model|GenericModel $this
 */
trait HasUriAttributes
{

    //region GETS
    protected function getUriAttribute()
    {
        if(empty($this->url)) {
            return null;
        }

        if(Str::startsWith($this->url, '#')) {
            return  $this->url;
        }

        return "{$this->http_protocol}:{$this->dynamic_uri}";
    }

    protected function getDynamicUriAttribute()
    {
        if(empty($this->url)) {
            return null;
        }
        if(Str::startsWith($this->url, '#')) {
            return  $this->url;
        }

        return ($this->site ?? false) ? ("{$this->site->dynamic_uri}{$this->url}") : '//'.$this->url;
    }

    protected function getUrlAttribute()
    {
        if(empty($this->attributes['url'])) {
            return null;
        }

        return (Str::of($this->attributes['url'])->startsWith('/') ? '' : '/') . $this->attributes['url'];
    }

    protected function getHttpProtocolAttribute()
    {
        return ($this->site->config->is_https ?? $this->config->is_https ?? false) ? 'https' : 'http';
    }
    //endregion

    //region HELPERS

    /**
     * @param HasUriAttributes $model
     *
     * @return string
     */
    public function urlFrom($model)
    {
        return ($model->url ?? '') . $this->url;
    }

    public function uriFrom($model, $dynamic = false)
    {
        return ($dynamic ? '' : "{$this->http_protocol}:") . '//' . $this->urlFrom($model);
    }

    public function uriTo($path)
    {
        return ($model->uri ?? '') . $path;
    }
    //endregion
}
