<?php
/*
 * Copyright (c) 2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Observers;

use Adminx\Common\Models\Article;
use Adminx\Common\Models\Category;
use Adminx\Common\Models\Menus\Menu;
use Adminx\Common\Models\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class MenuableObserver
{
    public function saved(Model|Page|Article|Category $model){

        if($model->menu_items()->count()){

            $updatedMenus = collect();

            foreach ($model->menu_items as $menuItem){
                $menuItem->loadUrlFromMenuable();

                if($menuItem->save() && !$updatedMenus->contains($menuItem->menu_id)){
                    $updatedMenus->add($menuItem->menu_id);
                }
            }

            if($updatedMenus->count()){
                foreach (Menu::whereIn('id', $updatedMenus->values()->toArray())->get() as $menu){
                    $menu->save();
                }
            }
        }
    }
}
