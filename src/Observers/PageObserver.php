<?php

namespace Adminx\Common\Observers;


use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Pages\Page;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PageObserver
{
    public function saving(Page $model)
    {
        //Gerar slug se estiver em branco
        if (empty($model->slug) && !$model->is_home) {
            $model->slug = $model->title;
        }

        //Gerar config caso esteja em branco
        if(!$model->has_config){
            $model->config = $model->type->config;
        }

        if ($model->id) {

            //Comprimir CSS e JS personalizado da Página
            $model->assets->minify();
        }

        if(empty($model->published_at)){
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
