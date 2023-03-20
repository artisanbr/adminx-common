<?php

namespace ArtisanBR\Adminx\Common\App\Observers;

use ArtisanBR\Adminx\Common\App\Models\Interfaces\OwneredModel;

class OwneredModelObserver
{

    public function creating(OwneredModel $model): void
    {
        $model->defineOwners();
    }
}
