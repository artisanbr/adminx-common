<?php

namespace ArtisanBR\Adminx\Common\App\Observers;

use ArtisanBR\Adminx\Common\App\Models\Category;
use ArtisanBR\Adminx\Common\App\Models\Page;
use ArtisanBR\Adminx\Common\App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class MenuableObserver
{
    public function saved(Model|Page|Post|Category $model){

        if($model->menu_items()->count()){
            foreach ($model->menu_items as $menuItem){
                $menuItem->loadUrlFromMenuable();
                $menuItem->save();
            }
        }
    }
}
