<?php

namespace Adminx\Common\Models\Traits;

use Adminx\Common\Models\HtmlBuild;
use Adminx\Common\Models\Interfaces\BuildableModel;

/**
 * @var BuildableModel $this
 */
trait HasHtmlBuilds
{

    public function builds()
    {
        return $this->morphMany(HtmlBuild::class, 'buildable');
    }

    public function build(){
        return $this->morphOne(HtmlBuild::class, 'buildable');
    }


}
