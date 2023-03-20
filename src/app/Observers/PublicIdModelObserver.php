<?php

namespace ArtisanBR\Adminx\Common\App\Observers;

use ArtisanBR\Adminx\Common\App\Models\Interfaces\PublicIdModel;

class PublicIdModelObserver
{
    //public $afterCommit = true;

    public function created(PublicIdModel $model): void
    {
        if (empty($model->public_id)) {

            $model->renewPublicId();

            $model->save();
        }
    }
}
