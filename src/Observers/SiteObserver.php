<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Observers;


use Adminx\Common\Models\Sites\Site;

class SiteObserver
{
    public function saved(Site $model): void
    {
        if ($model->theme ?? false) {
            $model->theme->saveAndBuild();
        }
    }
}
