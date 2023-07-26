<?php

namespace Adminx\Common\Observers;


use Adminx\Common\Models\Menu;

class MenuObserver
{
    public function saving(Menu $model)
    {
        //Gerar html
        $model->html = $model->mount_html();
    }

    public function saved(Menu $model): void
    {

    }
}
