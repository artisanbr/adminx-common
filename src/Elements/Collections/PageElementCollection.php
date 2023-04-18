<?php

namespace Adminx\Common\Elements\Collections;

use Adminx\Common\Models\ModelPageElement;
use Illuminate\Database\Eloquent\Collection;

class PageElementCollection extends Collection
{
    /**
     * The items contained in the collection.
     *
     * @var array<ModelPageElement>
     */
    protected $items = [];
    //region Customs

    public function html(){
        return $this->items->only('html')->join('\n');
    }

    //endregion
}
