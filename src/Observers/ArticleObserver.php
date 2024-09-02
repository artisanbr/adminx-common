<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

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

        $model->meta->frontend_build = $model->prepareFrontendBuild(true);
        $model->seo->html = $model->meta->frontend_build->seo->html;
        /*$model->meta->frontend_build->seo = $model->seo;
        $model->meta->frontend_build->seo->fill([
                                              'title'       => $model->seoTitle(),
                                              'description' => $model->getDescription(),
                                              'keywords'    => $model->seotKeywords(),
                                              'image_url'   => $model->seoImage(),
                                          ]);*/

        //dd($model->meta->frontend_build->seo,$model->meta->frontend_build->seo->html);

        /*if ($model->id) {

            //Comprimir CSS e JS personalizado da Página
            $model->assets->minify();
        }*/
    }

    /*public function saved(Post $model): void
    {

    }*/
}
