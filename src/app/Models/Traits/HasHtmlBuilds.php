<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits;

use ArtisanBR\Adminx\Common\App\Models\HtmlBuild;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\BuildableModel;

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
