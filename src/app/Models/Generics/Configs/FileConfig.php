<?php
namespace ArtisanBR\Adminx\Common\App\Models\Generics\Configs;

use Illuminate\Support\Facades\Auth;
use ArtisanLabs\GModel\GenericModel;

class FileConfig extends GenericModel
{

    protected $fillable = [
        'is_theme_bundle',
        'theme_bundle_position',
    ];

    protected $attributes = [
        'is_theme_bundle' => false,
    ];

    protected $casts = [
        'is_theme_bundle' => 'bool',
        'theme_bundle_position' => 'int',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

}
