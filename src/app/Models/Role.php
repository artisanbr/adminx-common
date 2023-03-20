<?php
namespace ArtisanBR\Adminx\Common\App\Models;


class Role extends \Spatie\Permission\Models\Role
{
    public static $defaults = [
        "root" => "Use Digital",
        "parceiro" => "Parceiro",
        "admin" => "Administrador",
        "user" => "Operador"
    ];

    protected $appends = [
        "permissions_list"
    ];

    protected function getPermissionsListAttribute(){
        return $this->permissions->pluck("name");
    }

    /**
     * @return object
     */
    public static function defaults() : object {
        return (object)self::$defaults;
    }
}
