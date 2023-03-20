<?php
namespace ArtisanBR\Adminx\Common\App\Models\Generics\Configs;

use ArtisanLabs\GModel\GenericModel;

class UserConfig extends GenericModel
{

    protected $fillable = [
        'receber_emails',
        'custom_permissions',
        'title',
    ];

    protected $attributes = [
        'receber_emails' => 1,
        'custom_permissions' => 0,
        'title' => 'Sr.',
    ];

    protected $casts = [
        'receber_emails' => 'bool',
        'custom_permissions' => 'bool'
    ];

    protected function getHasCustomPermissionsAttribute(){
        return $this->custom_permissions;
    }
}
