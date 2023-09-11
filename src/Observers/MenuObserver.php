<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Observers;


use Adminx\Common\Models\Menus\Menu;

class MenuObserver
{
    public function saving(Menu $model)
    {
        //Gerar html
        $model->html = $model->mount_html();
    }

    public function saved(Menu $model): void
    {

    }
}
