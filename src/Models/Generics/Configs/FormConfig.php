<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Configs;

use Adminx\Common\Models\Generics\Forms\FormSendButton;

class FormConfig extends GenericModel
{

    protected $fillable = [
        'send_mail',
        'show_title',
        'destinations',
        'mail_from_address',
        'mail_from_name',
        'mail_reply_to_address',
        'mail_reply_to_name',
        'on_sucess',
        'on_fail',
        'before_send',
        'send_button',
        //todo: js events, messagens de retorno,
    ];

    protected $attributes = [
        'send_mail' => true,
        'show_title' => true,
        'destinations' => [],
        'send_button' => [],
    ];

    protected $casts = [
        'send_mail' => 'bool',
        'show_title' => 'bool',
        'destinations' => 'collection',
        'mail_from_address' => 'string',
        'mail_from_name' => 'string',
        'mail_reply_to_address' => 'string',
        'mail_reply_to_name' => 'string',
        'on_sucess' => 'string',
        'send_button_text' => 'string',
        'send_button_attrs' => 'collection',
        'send_button' => FormSendButton::class,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    protected function getRenderSendButtonHtmlAttributesAttribute()
    {
        return $this->send_button_attrs->reduce(fn($carry, $value, $key) => $carry . $key . '="' . $value . '" ');
    }
}
