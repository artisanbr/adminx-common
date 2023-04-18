<?php

namespace Adminx\Common\Models\Traits;

use Illuminate\Foundation\Http\FormRequest;

trait HasValidation
{

    public static function createRules(FormRequest $request = null): array
    {
        return [];
    }

    public static function createMessages(FormRequest $request = null): array
    {
        return [];
    }

    public static function updateRules(FormRequest $request = null): array
    {
        return [];
    }

    public static function updateMessages(FormRequest $request = null): array
    {
        return [];
    }

    public static function allRules(FormRequest $request = null): array
    {
        $extra = [];
        if(@method_exists(self::class, 'extraRules')){
            $extra = @self::extraRules($request) ?? [];
        }
        return array_merge(self::createRules($request), self::updateRules($request), $extra);
    }

    public static function allMessages(FormRequest $request = null): array
    {
        $extra = [];
        if(@method_exists(self::class, 'extraMessages')){
            $extra = @self::extraMessages($request) ?? [];
        }
        return array_merge(self::createMessages($request), self::updateMessages($request), $extra);
    }
}
