<?php

namespace Adminx\Common\Observers;

use Adminx\Common\Models\Interfaces\OwneredModel;

class OwneredModelObserver
{

    public function creating(OwneredModel $model): void
    {
        $model->defineOwners();
    }
}
