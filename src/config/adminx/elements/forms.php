<?php

use ArtisanBR\Adminx\Common\App\Enums\Forms\FormElementType;

return [
    'element-types' => [
        FormElementType::HiddenField->value => [

        ]
    ],
    'elements' => [
        'attributes' => [
            'required' => 'Obrigatório',
            'multiple' => 'Multi-seleção',
        ]
    ],
    'embed' => [
        'tags' => [
            'module:form',
            'form-embed',
            'formembed',
            'custom-form',
            'customform',
        ],
        'shortcodes' => [
            '{{form}}',
            '{{form-embed}}',
            '{{formembed}}',
            '{{custom-form}}',
            '{{customform}}',
            '~form',
            '~form-embed',
            '~formembed',
            '~custom-form',
            '~customform',
        ]
    ],
];
