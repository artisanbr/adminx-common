<?php
/*
 * Copyright (c) 2024-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Configs;

use Adminx\Common\Enums\Forms\FormCaptchaType;
use ArtisanLabs\GModel\GenericModel;

class CaptchaConfig extends GenericModel
{

    protected $fillable = [
        'enabled',
        'keys',
        'type',
    ];

    protected $attributes = [
        'enabled' => true,
        'keys' => [],
        'type' => FormCaptchaType::RecaptchaV2->value,
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'keys' => 'collection',
        'type' => FormCaptchaType::class,
    ];

    protected $appends = [
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
