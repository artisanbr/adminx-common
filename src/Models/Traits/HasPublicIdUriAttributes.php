<?php
/*
 * Copyright (c) 2024-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits;

use ArtisanLabs\GModel\GenericModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model|GenericModel $this
 */
trait HasPublicIdUriAttributes
{

    //region GETS
    protected function getPublicIdUriAttribute()
    {
        if(empty($this->public_id)) {
            return null;
        }

        return "{$this->http_protocol}:{$this->getPublicIdDynamicUriAttribute()}";
    }

    protected function getPublicIdDynamicUriAttribute(): ?string
    {

        if (blank($this->attributes['dynamic_permanent_uri'] ?? null)) {

            if(empty($this->public_id) && !($this->site ?? false) && blank($this->dynamic_uri ?? null)) {
                return null;
            }

            $this->attributes['dynamic_permanent_uri'] = str($this->site?->dynamic_uri ?? $this->dynamic_uri ?? '')->append($this->getPermanentUrlAttribute())->toString();

        }



        return $this->attributes['dynamic_permanent_uri'] ?? null;
    }

    protected function getPublicIdUrlAttribute(): ?string
    {
        return $this->getPermanentUrlAttribute();
    }

    protected function getPermanentUriAttribute()
    {

        if (blank($this->attributes['permanent_uri'] ?? null)) {

            if (blank($this->parent_id) && $this->is_home) {

                $this->attributes['permanent_uri'] = $this->site->uri;

            }
            else {

                if ($this->parent_id && $this->parent->exists()) {

                    $urlId = str($this->parent->slug)->finish('/')->append($this->slug);

                }else{
                    $urlId = str($this->slug);
                }

                $this->attributes['permanent_uri'] = $this->site->uriTo($urlId->toString());
            }

        }

        return $this->attributes['uri'] ?? '';


    }

    protected function getPermanentUrlAttribute()
    {

        if (blank($this->attributes['permanent_url'] ?? null)) {

            if (empty($this->public_id)) {
                $this->attributes['permanent_url'] = null;
            }
            else {
                $this->attributes['permanent_url'] = str($this->public_id)->start('/')->finish('/')->toString();
            }
        }

        return $this->attributes['permanent_url'] ?? '';


    }
    //endregion

    //region HELPERS

    /**
     * @param HasUriAttributes $model
     *
     * @return string
     */
    public function PublicIdUrlFrom($model)
    {
        return ($model->public_id_url ?? '') . $this->public_id_url;
    }

    public function PublicIdUriFrom($model, $dynamic = false)
    {
        return ($dynamic ? '' : "{$this->http_protocol}:") . '//' . $this->PublicIdUrlFrom($model);
    }

    public function PublicIdUrlTo($path)
    {
        return $this->public_id_url . $path;
    }

    public function PublicIdUriTo($path)
    {
        return $this->public_id_uri . $path;
    }
    //endregion
}
