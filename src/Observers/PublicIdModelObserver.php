<?php

namespace Adminx\Common\Observers;

use Adminx\Common\Models\Interfaces\PublicIdModel;

class PublicIdModelObserver
{
    //public $afterCommit = true;

    public function saved(PublicIdModel $model): void
    {
        if (empty($model->public_id)) {

            $model->renewPublicId();

            $model->save();
        }
    }
}
