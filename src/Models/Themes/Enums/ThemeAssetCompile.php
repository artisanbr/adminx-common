<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Enums;

use Adminx\Common\Enums\Traits\EnumBase;

enum ThemeAssetCompile: string
{
    use EnumBase;

    case Js = 'js';
    case Css = 'css';
    case All = 'all';
    case Disabled = 'disabled';
}
