<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
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
        if(empty($this->public_id_url)) {
            return null;
        }

        return "{$this->http_protocol}:{$this->public_id_dynamic_uri}";
    }

    protected function getPublicIdDynamicUriAttribute(): ?string
    {
        if(empty($this->public_id_url)) {
            return null;
        }

        return str($this->site->dynamic_uri)->append($this->public_id_url)->toString();
    }

    protected function getPublicIdUrlAttribute(): ?string
    {
        if(empty($this->public_id)) {
            return null;
        }

        return str($this->public_id)->start('/')->finish('/')->toString();
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
