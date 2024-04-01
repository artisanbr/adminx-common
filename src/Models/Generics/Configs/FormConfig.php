<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Configs;

use Adminx\Common\Models\Generics\Forms\FormRecipient;
use Adminx\Common\Models\Generics\Forms\FormSendButton;
use ArtisanBR\GenericModel\Casts\AsCollectionOf;
use ArtisanBR\GenericModel\Model as GenericModel;

class FormConfig extends GenericModel
{

    protected $fillable = [
        'send_mail', //Enviar respostas por email
        'allow_select_recipient', //Permitir selecionar destinos de entrega
        'select_recipient_title', //Permitir selecionar destinos de entrega
        'enable_recaptcha', //Permitir selecionar destinos de entrega
        'show_title',
        'destinations',
        'recipients',
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
        'send_mail'    => true,
        'allow_select_recipient'    => false,
        'select_recipient_title'    => 'Selecione um destinatário',
        'enable_recaptcha'    => true,
        'show_title'   => true,
        'destinations' => [],
        'recipients'   => [],
        'send_button'  => [],
    ];

    protected $casts = [
        'send_mail'             => 'bool',
        'enable_recaptcha'             => 'bool',
        'allow_select_recipient'             => 'bool',
        'select_recipient_title'             => 'string',
        'show_title'            => 'bool',
        'destinations'          => 'collection',
        'recipients'            => AsCollectionOf::class . ':' . FormRecipient::class,
        'mail_from_address'     => 'string',
        'mail_from_name'        => 'string',
        'mail_reply_to_address' => 'string',
        'mail_reply_to_name'    => 'string',
        'on_sucess'             => 'string',
        'send_button_text'      => 'string',
        'send_button_attrs'     => 'collection',
        'send_button'           => FormSendButton::class,
    ];

    public function __construct(array $attributes = [])
    {

        if (isset($attributes['destinations']) && !collect($attributes['recipients'] ?? [])->filter()->toArray()) {
            //Get Recipients from destinations (todo: remove destinations)
            $attributes['recipients'] = collect($attributes['destinations'])->map(fn($recipient) => [
                'address' => $recipient,
            ])->toArray();
        }
        parent::__construct($attributes);
    }

    protected function getRenderSendButtonHtmlAttributesAttribute()
    {
        return $this->send_button_attrs->reduce(fn($carry, $value, $key) => $carry . $key . '="' . $value . '" ');
    }
}
