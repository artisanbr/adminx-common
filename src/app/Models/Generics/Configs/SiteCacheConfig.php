<?php
namespace ArtisanBR\Adminx\Common\App\Models\Generics\Configs;

use ArtisanLabs\GModel\GenericModel;

class SiteCacheConfig extends GenericModel
{

    protected $fillable = [
        'enable_model',
        'enable_view',

        'clear_model',
        'clear_view',
    ];

    protected $attributes = [
        'enable_model' => false,
        'enable_view' => false,

        'clear_model' => false,
        'clear_view' => false,
    ];

    protected $casts = [
        'enable_model' => 'bool',
        'enable_view' => 'bool',

        'clear_model' => 'bool',
        'clear_view' => 'bool',
    ];

    //region ATTRIBUTES

    public function clearAll(): static
    {
        $this->attributes = [...$this->attributes, 'clear_model' => true, 'clear_view' => true];

        return $this;
    }

}
