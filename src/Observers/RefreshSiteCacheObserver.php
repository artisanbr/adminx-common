<?php

namespace Adminx\Common\Observers;

use Adminx\Common\Models\Interfaces\OwneredModel;

class RefreshSiteCacheObserver
{
    public function saved(OwneredModel $model){
        /*if($model->site){
            $model->site->config->cache->clearAll();
            $model->site->save();
        }*/
    }
}
