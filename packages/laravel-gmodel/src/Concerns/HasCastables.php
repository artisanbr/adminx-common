<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace ArtisanLabs\GModel\Concerns;

trait HasCastables
{
    protected $casts = [];

    public function getCasts(){
        return $this->casts;
    }
}
