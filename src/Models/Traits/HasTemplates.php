<?php

namespace Adminx\Common\Models\Traits;

use Adminx\Common\Libs\Helpers\MorphHelper;
use Adminx\Common\Models\Templates\Templatable;
use Adminx\Common\Models\Templates\Template;


trait HasTemplates
{
    public function modelTemplate()
    {
        return $this->hasOne(Templatable::class, 'templatable_id', 'id')->where('templatable_type', MorphHelper::getMorphTypeTo(self::class));
    }


}
