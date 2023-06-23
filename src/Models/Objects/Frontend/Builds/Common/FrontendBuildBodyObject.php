<?php

namespace Adminx\Common\Models\Objects\Frontend\Builds\Common;

use Adminx\Common\Models\Objects\Frontend\Builds\Common\Abstract\AbstractFrontendBuildSectionObject;

/**
 * @property string $html
 * @property string $minify
 */
class FrontendBuildBodyObject extends AbstractFrontendBuildSectionObject
{

    public function __construct(array $attributes = [])
    {

        $this->addFillables([
                                'class',
                                'id',
                            ]);

        $this->addCasts([
                            'class' => 'string',
                            'id'    => 'string',
                        ]);

        parent::__construct($attributes);
    }


}
