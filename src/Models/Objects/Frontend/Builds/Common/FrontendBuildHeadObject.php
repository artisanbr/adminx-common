<?php

namespace Adminx\Common\Models\Objects\Frontend\Builds\Common;


use Adminx\Common\Models\Objects\Frontend\Builds\Common\Abstract\AbstractFrontendBuildSectionObject;

/**
 * @property string $html
 * @property string $minify
 */
class FrontendBuildHeadObject extends AbstractFrontendBuildSectionObject
{

    public function __construct(array $attributes = [])
    {

        $this->addFillables([
                                'gtag_script',
                                'css',
                            ]);

        $this->addCasts([
                            'gtag_script' => 'string',
                            'css'         => 'string',
                        ]);

        $this->addAttributes([
                                 'gtag_script' => '',
                                 'css'         => '',
                             ]);

        parent::__construct($attributes);
    }


}
