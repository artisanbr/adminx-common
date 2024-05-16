<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Enums;

use Adminx\Common\Enums\Traits\EnumBase;
use Adminx\Common\Enums\Traits\EnumWithTitles;

enum ThemeBundleDefaults: string
{
    use EnumBase, EnumWithTitles;

    case BodyJsMain = 'body_js_main';
    case BodyJsDefer = 'body_js_defer';

    case HeadJsMain = 'head_js_main';
    case HeadJsDefer = 'head_js_defer';

    case CssMain = 'css_main';
    case CssDefer = 'css_defer';


    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::CssMain => 'CSS Principal',
            self::CssDefer => 'CSS Adiado',
            self::BodyJsMain => 'JS Principal na tag <code>&lt;body&gt;</code>',
            self::BodyJsDefer => 'JS Adiado na tag <code>&lt;body&gt;</code>',
            self::HeadJsMain => 'JS Principal na tag <code>&lt;head&gt;</code>',
            self::HeadJsDefer => 'JS Adiado na tag <code>&lt;head&gt;</code>',
        };
    }

    public static function getNameTo($type): string
    {
        return match ($type) {
            self::BodyJsMain, self::CssMain => 'main',
            self::BodyJsDefer, self::CssDefer => 'defer',
            self::HeadJsMain => 'head-main',
            self::HeadJsDefer => 'head-defer',
        };
    }

    public function name(): string {
        return self::getNameTo($this);
    }

    public static function getPlacementTo($type): ThemeBundlePlacement
    {
        return match ($type) {
            self::CssDefer, self::CssMain => ThemeBundlePlacement::Css,
            self::BodyJsMain, self::BodyJsDefer => ThemeBundlePlacement::BodyJs,
            self::HeadJsMain, self::HeadJsDefer => ThemeBundlePlacement::HeadJs,
        };
    }

    public function placement(): ThemeBundlePlacement
    {
        return self::getPlacementTo($this);
    }


    public static function getDeferTo($type): bool
    {
        return match ($type) {
            self::CssMain, self::BodyJsMain, self::HeadJsMain => false,
            self::CssDefer, self::BodyJsDefer, self::HeadJsDefer => true,
        };
    }

    public function defer(): bool
    {
        return self::getDeferTo($this);
    }

    public function defaults(array $merge = []): array
    {
        return [
            'name' => $this->name(),
            'placement' => $this->placement()->value,
            'defer' => $this->defer(),
            ...$merge,
        ];
        //return self::getTypeTo($this);
    }

}
