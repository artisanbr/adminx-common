<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\View\Frontend\Components\Layouts;
use Illuminate\View\Component;

class Frontend extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render()
    {

        return view('common-frontend::layout.base');
    }
}
