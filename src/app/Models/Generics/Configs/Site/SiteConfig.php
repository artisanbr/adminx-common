<?php
namespace ArtisanBR\Adminx\Common\App\Models\Generics\Configs\Site;

use ArtisanLabs\GModel\GenericModel;

class SiteConfig extends GenericModel
{

    protected $fillable = [
        'cache',
        'mail',
        'is_https',
        'debug',
        'recaptcha_site_key',
        'recaptcha_private_key',
        'enable_html_minify',
        'enable_image_optimize',
    ];

    protected $attributes = [
        'enable_html_minify' => false,
        'enable_image_optimize' => true,
        'debug' => false,
        'is_https' => false,
        'cache' => [],
    ];

    protected $casts = [
        'cache' => SiteCacheConfig::class,
        'mail' => MailServerConfig::class,
        'is_https' => 'bool',
        'debug' => 'bool',
        'enable_html_minify' => 'bool',
        'enable_image_optimize' => 'bool',
    ];

    //region ATTRIBUTES

    //region GETS
    protected function getRecaptchaSiteKeyAttribute($value){
        return $value ?? config("services.recaptcha.site_key");
    }
    protected function getRecaptchaPrivateKeyAttribute($value){
        return $value ?? config("services.recaptcha.private_key");
    }
    //endregion
    //endregion
}
