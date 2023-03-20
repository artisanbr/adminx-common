<?php
namespace ArtisanBR\Adminx\Common\App\Models;


class Permission extends \Spatie\Permission\Models\Permission
{

    protected $appends = [
        "roles_list"
    ];


    protected function getRolesListAttribute(){
        return $this->roles->pluck("id");
    }
}
