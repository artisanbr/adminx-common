<?php

namespace Adminx\Common\View\Common\Components;

use Illuminate\View\Component;

class Alert extends Component
{

    public function __construct(
        public $title = null,
        public $color = 'primary',
        public $bgType = 'light',
        public $noClose = false,
        public $icon = null,
        public $iconSize = '2hx',
        public $iconColor = null,
        public $iconClass = null,
        public $titleClass = null,
    )
    {

    }

    public function render()
    {
        return function (array $data) {
            // $data['componentName'];
            // $data['attributes'];
            // $data['slot'];

            return 'adminx-common::components.alert';
        };
    }

    //region METHODS

    public function titleClass(): string {
        $finalClass = '';

        $finalClass .= match ($this->bgType) {
            //'light' => "",
            'solid' => " light",
            default => " text-{$this->color}",
        };

        $finalClass .= " {$this->titleClass}";

        return $finalClass;
    }

    public function computedClass(): string
    {
        $finalClass = '';

        $finalClass .= match ($this->bgType) {
            'light' => " bg-light-{$this->color}",
            'solid' => " bg-{$this->color}",
            default => " alert-{$this->color}",
        };

        if($this->noClose){
            $finalClass .= ' alert-dismissible';
        }

        if($this->icon){
            $finalClass .= ' d-flex align-items-center';
        }

        return $finalClass;
    }

    //endregion
}
