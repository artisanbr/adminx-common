<?php

namespace Adminx\Common\Models\Traits;

use Adminx\Common\Libs\Support\Str;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model|GenericModel $this
 */
trait HasRelatedCache
{

    //region GETS
    public function relatedCacheName($name): string
    {
        return Str::slug($this->public_id ?? $this->id ?? 'tempRelatedCache-'.time()) . '__' . Str::slug($name);
    }
}
