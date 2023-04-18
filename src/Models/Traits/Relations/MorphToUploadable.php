<?php

namespace Adminx\Common\Models\Traits\Relations;

trait MorphToUploadable
{
    public function uploadable()
    {
        return $this->morphTo(__FUNCTION__, __FUNCTION__ . '_type', __FUNCTION__ . '_id');
    }
}
