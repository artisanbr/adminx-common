<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits\Relations;

use ArtisanBR\Adminx\Common\App\Models\Widget;
use ArtisanBR\Adminx\Common\App\Models\Widgeteable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasWidgets
{
    /**
     * @return MorphToMany
     */
    public function widgets(): MorphToMany
    {
        return $this->morphToMany(Widget::class, 'widgeteable')->using(Widgeteable::class);
    }
}
