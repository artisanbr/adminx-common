<?php

namespace Adminx\Common\Observers;


use Adminx\Common\Models\Site;

class SiteObserver
{
    public function saved(Site $model): void
    {
        if ($model->theme ?? false) {
            $model->theme->saveAndCompile();
        }
    }
}
