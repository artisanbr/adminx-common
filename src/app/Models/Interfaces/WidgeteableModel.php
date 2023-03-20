<?php

namespace ArtisanBR\Adminx\Common\App\Models\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 */
interface WidgeteableModel
{
    /**
     * @return MorphToMany
     */
    public function widgets(): MorphToMany;
}
