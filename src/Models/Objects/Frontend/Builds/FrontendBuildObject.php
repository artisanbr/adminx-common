<?php

namespace Adminx\Common\Models\Objects\Frontend\Builds;

use Adminx\Common\Models\Objects\Frontend\Builds\Common\FrontendBuildBodyObject;
use Adminx\Common\Models\Objects\Frontend\Builds\Common\FrontendBuildHeadObject;
use ArtisanLabs\GModel\GenericModel;
use Butschster\Head\Contracts\Packages\ManagerInterface;
use Butschster\Head\MetaTags\Meta;

/**
 * @property string $html
 * @property string $minify
 */
class FrontendBuildObject extends GenericModel
{

    protected $fillable = [
        'meta',
        'lang',
        'head',
        'body',
    ];

    protected $casts = [
        'lang'   => 'string',
        'head'   => FrontendBuildHeadObject::class,
        'body' => FrontendBuildBodyObject::class,
        'meta' => null,
    ];

    protected $attributes = [
        /*body' => [],*/
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->meta = new Meta(
            app(ManagerInterface::class),
            app('config')
        );

        $this->meta->addCsrfToken();
        $this->meta->initialize();
    }

    protected function getLangAttribute(){
        return $this->attributes['lang'] ?? str_replace('_', '-', app()->getLocale());
    }

}
