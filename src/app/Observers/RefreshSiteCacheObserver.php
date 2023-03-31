<?php

namespace ArtisanBR\Adminx\Common\App\Observers;

use ArtisanBR\Adminx\Common\App\Models\Interfaces\OwneredModel;

class RefreshSiteCacheObserver
{
    public function saved(OwneredModel $model){
        if($model->site){
            $model->site->config->cache->clearAll();
            $model->site->save();
        }
    }
}
