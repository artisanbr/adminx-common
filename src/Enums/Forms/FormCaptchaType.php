<?php
/*
 * Copyright (c) 2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Enums\Forms;

use ArtisanBR\Goodies\Enums\Traits\EnumBase;
use ArtisanBR\Goodies\Enums\Traits\EnumWithTitles;

enum FormCaptchaType: string
{
    use EnumBase, EnumWithTitles;

    //FIELDS
    case RecaptchaV2 = 'recaptcha-v2';
    case RecaptchaV3 = 'recaptcha-v3';
    case Cloudflare = 'cloudflare';


    public function keys(): array
    {
        return self::getKeysTo($this);
    }

    public static function getKeysTo($type): array
    {
        return match ($type) {
            self::RecaptchaV2, self::RecaptchaV3 => ['site_key', 'secret_key'],
            self::Cloudflare => [/*'token', 'secret'*/],

        };
    }

    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::RecaptchaV2 => 'Recaptcha V2',
            self::RecaptchaV3 => 'Recaptcha V3 (Em Desenvolvimento)',
            self::Cloudflare => 'Cloudflare (Em Desenvolvimento)',

        };
    }

    public static function allKeys(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            function (self $item) {
                return $item->keys();
            },
            self::cases()
        ));
    }
}
