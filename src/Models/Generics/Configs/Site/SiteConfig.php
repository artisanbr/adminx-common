<?php
namespace Adminx\Common\Models\Generics\Configs\Site;

use ArtisanLabs\GModel\GenericModel;

class SiteConfig extends GenericModel
{

    protected $fillable = [
        'performance',
        'mail',
        'is_https',
        'ssl',
        'debug',

        'maintenance',

        'recaptcha_site_key',
        'recaptcha_private_key',
    ];

    protected $attributes = [
        'maintenance' => false,
        'debug' => false,
        'ssl' => false,
        'performance' => [],
    ];

    protected $casts = [
        'performance' => SitePerformanceConfig::class,
        'mail' => MailServerConfig::class,
        'is_https' => 'bool',
        'ssl' => 'bool',
        'debug' => 'bool',
        'maintenance' => 'bool',
    ];



    //region ATTRIBUTES

    protected function setIsHttpsAttribute($value){
        return $this->attributes['ssl'] = $value;
    }

    protected function getIsHttpsAttribute(){
        return $this->ssl;
    }

    protected function getSslAttribute(){
        return $this->attributes['ssl'] ?? $this->attributes['is_https'] ?? false;
    }

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
