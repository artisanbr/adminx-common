<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits;

use Adminx\Common\Libs\Support\Str;
use ArtisanBR\GenericModel\Model as GenericModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model|GenericModel $this
 */
trait HasRelatedCache
{

    //region GETS
    public function relatedCacheName($name): string
    {
        return Str::slug($this->public_id ?? $this->id ?? 'tempRelatedCache-'.time()) . '__' . Str::slug($name);
    }
}
