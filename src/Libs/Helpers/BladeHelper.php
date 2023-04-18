<?php


namespace Adminx\Common\Libs\Helpers;


use Illuminate\Support\Facades\Blade;
use Illuminate\View\Factory;
use function Adminx\Common\Libs\Helpers\ends_with;
use function Adminx\Common\Libs\Helpers\starts_with;

class BladeHelper
{
    public static function traits($variable){

        if(starts_with($variable, ['"', "'"])) $variable = substr($variable, 1);

        if(ends_with($variable, ['"', "'"])) $variable = substr($variable, 0, -1);

        return $variable;
    }

    public static function bladeCompile(string $value, array $args = array()): bool|string
    {
        $generated = Blade::compileString($value);
        $args['__env'] = app(Factory::class);

        ob_start() and extract($args, EXTR_SKIP);

        // We'll include the view contents for parsing within a catcher
        // so we can avoid any WSOD errors. If an exception occurs we
        // will throw it out to the exception handler.
        try
        {
            eval('?>'.$generated);
        }

            // If we caught an exception, we'll silently flush the output
            // buffer so that no partially rendered views get thrown out
            // to the client and confuse the user with junk.
        catch (\Exception $e)
        {
            ob_get_clean(); throw $e;
        }

        $content = ob_get_clean();

        return $content;
    }
}
