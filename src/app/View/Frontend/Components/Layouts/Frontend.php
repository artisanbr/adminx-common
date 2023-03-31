<?php

namespace ArtisanBR\Adminx\Common\App\View\Frontend\Components\Layouts;
use Illuminate\View\Component;

class Frontend extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render()
    {

        return view('adminx-frontend::layout.base');
    }
}
