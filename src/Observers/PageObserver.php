<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Observers;


use Adminx\Common\Models\Pages\Objects\PageConfig;
use Adminx\Common\Models\Pages\Page;
use Carbon\Carbon;

class PageObserver
{
    public function saving(Page $model)
    {
        //Gerar slug se estiver em branco
        if (empty($model->slug) && !$model->is_home) {
            $model->slug = $model->title;
        }

        //Gerar config caso esteja em branco
        if (!$model->has_config) {
            $model->config = $model->type?->config ?? new PageConfig();
        }

        if ($model->id) {

            //Comprimir CSS e JS personalizado da Página
            $model->assets->minify();

            //region Seo
            /*if (empty($model->seo->keywords)) {
                $model->seo->keywords = $model->content->keywords;
            }

            if (empty($model->seo->description)) {
                $model->seo->description = $model->content->description;
            }

            if (empty($model->seo->title)) {
                $model->seo->title = $model->title;
            }*/
            //endregion
        }

        if (empty($model->published_at)) {
            $model->published_at = $model->created_at ?? Carbon::now();
        }
    }

    public function saved(Page $model): void
    {
        $model->load(['site']);

        //Definir página inicial
        if ($model->is_home) {
            //Tirar demais páginas iniciais
            $model->site->pages()
                        ->where('is_home', true)
                        ->whereNot('id', $model->id)
                        ->update([
                                     'is_home' => false,
                                 ]);
        }
    }
}
