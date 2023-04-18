<?php

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\Widget;
use Adminx\Common\Models\Widgeteable;
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
