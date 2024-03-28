<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Sites\Site;
use ArtisanBR\GenericModel\Model as GenericModel;
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

        if(get_class($this) === Site::class){
            return '//'.$this->url;
        }

        return (($this->attributes['site_id'] ?? false) && $this->site ? $this->site->dynamic_uri : '').$this->url;
    }

    protected function getUrlAttribute()
    {
        if(empty($this->attributes['url'])) {
            return null;
        }

        return (Str::of($this->attributes['url'])->startsWith('/') ? '' : '/') . $this->attributes['url'] . '/';
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

    public function uriTo($path, $endWithDash = true)
    {
        return $this->uri . $this->traitPath($path, $endWithDash, 'uri');
    }

    public function urlTo($path, $endWithDash = true)
    {
        /*if(Str::startsWith($path, '/') && Str::endsWith($this->url, '/')){
            $path = Str::substr($path, 0, -1);
        }*/
        return $this->url . $this->traitPath($path, $endWithDash);
    }

    protected function traitPath(?string $path, $endWithDash = true, $comparesWithAttr = 'url'): string
    {
        //$path = (Str::startsWith($path, '/') && Str::endsWith($this->{$comparesWithAttr}, '/')) ? Str::substr($path, 0, 1) : $path;
        $pathCollection = collect(explode('/', (string) $path))->filter();
        $path = $pathCollection->implode('/');

        //Check if is file
        $lastPath = $pathCollection->last();

        if(!empty($path) && ($endWithDash || !Str::contains($lastPath,'.'))){
            //$path .= !Str::endsWith($this->{$path}, '/') ? '/' : '';
            $path .= '/';
        }

        if(!empty($path) && !Str::endsWith($this->{$comparesWithAttr}, '/') && !Str::startsWith($path, '/')){
            //$path .= !Str::endsWith($this->{$path}, '/') ? '/' : '';
            $path = '/'.$path;
        }
        /*else{
            $path = Str::endsWith($path, '/') ? Str::substr($path, 0, -1) : $path;
        }*/

        return $path;
    }
    //endregion
}
