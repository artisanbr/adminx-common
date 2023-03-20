<?php

namespace ArtisanBR\Adminx\Common\App\Observers;

use ArtisanBR\Adminx\Common\App\Libs\Support\Str;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\HtmlModel;
use ArtisanBR\Adminx\Common\App\Models\Site;
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
