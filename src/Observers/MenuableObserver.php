<?php

namespace Adminx\Common\Observers;

use Adminx\Common\Models\Category;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Article;
use Illuminate\Database\Eloquent\Model;

class MenuableObserver
{
    public function saved(Model|Page|Article|Category $model){

        if($model->menu_items()->count()){
            foreach ($model->menu_items as $menuItem){
                $menuItem->loadUrlFromMenuable();
                $menuItem->save();
            }
        }
    }
}
