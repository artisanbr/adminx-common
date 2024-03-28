<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits;

use ArtisanBR\GenericModel\GenericModel;
use Delight\Random\Random;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model|GenericModel $this
 */
trait HasPublicIdAttribute
{

    //region HELPERS

    /**
     * Gerar uma chave pública única
     * @return string
     */
    public function generatePublicId(): string
    {
        $startHash = $this->id ?? 0;

        if(@$this->user_id ?? false){
            $startHash .= $this->user_id;
        }

        return base_convert($startHash . round(time() / 100), 10, 30) . Random::base32String(3);
    }

    /**
     * Renovar a chave pública desta model
     * @return void
     */
    public function renewPublicId(): void
    {
        $this->attributes['public_id'] = $this->generatePublicId();
    }

    //endregion
}
