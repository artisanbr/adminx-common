<?php

namespace Adminx\Common\Observers;


use Adminx\Common\Models\Article;
use Carbon\Carbon;

class ArticleObserver
{
    public function saving(Article $model)
    {
        //Gerar slug se estiver em branco
        if (empty($model->slug)) {
            $model->slug = $model->title;
        }

        if(empty($model->published_at)){
            $model->published_at = $model->created_at ?? Carbon::now();
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
