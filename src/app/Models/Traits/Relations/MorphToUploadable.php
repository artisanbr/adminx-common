<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits\Relations;

trait MorphToUploadable
{
    public function uploadable()
    {
        return $this->morphTo(__FUNCTION__, __FUNCTION__ . '_type', __FUNCTION__ . '_id');
    }
}
