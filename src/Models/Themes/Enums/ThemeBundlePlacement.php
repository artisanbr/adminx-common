<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Enums;

use Adminx\Common\Enums\Traits\EnumBase;

enum ThemeBundlePlacement: string
{
    use EnumBase;

    case BodyJs = 'body_js';
    case HeadJs = 'head_js';
    case Css = 'css';

    public static function getExtensionTo($type): string
    {
        return match ($type) {
            self::BodyJs, self::HeadJs => '.js',
            self::Css => '.css',
        };
    }


    public function extension(): string
    {
        return self::getExtensionTo($this);
    }
}
