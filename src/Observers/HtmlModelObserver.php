<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Observers;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Interfaces\HtmlModel;
use Adminx\Common\Models\Sites\Site;
use Illuminate\Database\Eloquent\Model;

class HtmlModelObserver
{
    public function saved(Model|HtmlModel $model): void
    {
        //Armazenar HTML e Advanced HTML em banco
        $newAdvanced = $model->builtHtml();

        if (!Str::of($newAdvanced)->exactly($model->html)) {
           /* $model->update([
                               'html' => $newAdvanced,
                           ]);*/
            $model->flushHtmlCache();
            $model->save();
        }
    }
}
