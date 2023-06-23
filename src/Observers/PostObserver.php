<?php

namespace Adminx\Common\Observers;


use Adminx\Common\Models\Post;

class PostObserver
{
    public function saving(Post $model)
    {
        //Gerar slug se estiver em branco
        if (empty($model->slug)) {
            $model->slug = $model->title;
        }

        /*if ($model->id) {

            //Comprimir CSS e JS personalizado da Página
            $model->assets->minify();
        }*/
    }

    /*public function saved(Post $model): void
    {

    }*/
}
