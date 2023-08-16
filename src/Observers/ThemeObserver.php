<?php

namespace Adminx\Common\Observers;


use Adminx\Common\Models\Themes\Theme;

class ThemeObserver
{
    public function saving(Theme $model)
    {
        if ($model->id) {
            //Comprimir CSS e JS personalizado da Página
            $model->assets->minify();
        }
    }

    /*public function saved(Theme $model): void
    {
        //$model->compile();
    }*/
}
