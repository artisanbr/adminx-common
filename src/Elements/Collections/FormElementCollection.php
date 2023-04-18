<?php

namespace Adminx\Common\Elements\Collections;

use Adminx\Common\Elements\Forms\FormElement;
use Illuminate\Support\Collection;

class FormElementCollection extends Collection
{
    /**
     * The items contained in the collection.
     *
     * @var FormElement[]
     */
    protected $items = [];

    public function __construct($items = [])
    {
        if (count($items) && is_array($items[array_key_first($items)])) {
            $this->items = collect($items)->mapInto(FormElement::class)->all();
        }else{
            parent::__construct($items);
        }
    }


    //region Customs

    public function html()
    {

        //Todo: html do form aqui

        return $this->items->only('html')->join('\n');
    }

    //endregion
}
