<?php
namespace ArtisanBR\Adminx\Common\App\Models\Generics\Configs;

use ArtisanLabs\GModel\GenericModel;

class SiteConfig extends GenericModel
{

    protected $fillable = [
        'cache',
        'is_https',
        'debug',
        'recaptcha_site_key',
        'recaptcha_private_key',
        'mail',
    ];

    protected $attributes = [
        'debug' => false,
        'is_https' => false,
        'cache' => [],
    ];

    protected $casts = [
        'cache' => SiteCacheConfig::class,
        'is_https' => 'bool',
        'debug' => 'bool',
        'mail' => MailServerConfig::class,
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
