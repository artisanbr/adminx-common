<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Sites\Site;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model|GenericModel $this
 */
trait HasUriAttributes
{
    protected function uri(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->generateUri(),
        );

    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->generateUrl(),
        );

    }

    //region GETS
    protected function generateUri()
    {
        if (empty($this->url)) {
           return null;
        }

        if (Str::startsWith($this->url, '#')) {
            return $this->url;
        }

        return "{$this->http_protocol}:{$this->dynamic_uri}";

    }

    protected function getDynamicUriAttribute()
    {

        if (blank($this->temporaryAttributes['dynamic_uri'] ?? null)) {
            if (empty($this->url)) {
                $this->temporaryAttributes['dynamic_uri'] = null;
            }
            else if (Str::startsWith($this->url, '#')) {
                $this->temporaryAttributes['dynamic_uri'] = $this->url;
            }
            else if (get_class($this) === Site::class) {
                $this->temporaryAttributes['dynamic_uri'] = '//' . $this->url;
            }
            else {
                $this->temporaryAttributes['dynamic_uri'] = (($this->attributes['site_id'] ?? false) && $this->site ? $this->site->dynamic_uri : '') . $this->url;
            }
        }

        return $this->temporaryAttributes['dynamic_uri'];
    }

    protected function generateUrl()
    {
        if (empty($this->attributes['url'] ?? null)) {
            return null;
        }

        return str($this->attributes['url'])->start('/')->finish('/')->toString();
    }

    protected function getHttpProtocolAttribute()
    {
        if (blank($this->temporaryAttributes['http_protocol'] ?? null)) {
            $this->temporaryAttributes['http_protocol'] = ($this->site->config->is_https ?? $this->config->is_https ?? false) ? 'https' : 'http';
        }

        return $this->temporaryAttributes['http_protocol'];
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
        $pathCollection = collect(explode('/', (string)$path))->filter();
        $path = str($pathCollection->implode('/'));

        //Check if is file
        $lastPath = str($pathCollection->last());

        if ($path->isNotEmpty() && ($endWithDash || !$lastPath->contains('.'))) {
            $path = $path->finish('/');
        }

        if ($path->isNotEmpty() && !Str::endsWith($this->{$comparesWithAttr}, '/') && !Str::startsWith($path, '/')) {
            //$path .= !Str::endsWith($this->{$path}, '/') ? '/' : '';
            $path = $path->start('/');
        }

        /*else{
            $path = Str::endsWith($path, '/') ? Str::substr($path, 0, -1) : $path;
        }*/

        return $path->toString();
    }
    //endregion
}
