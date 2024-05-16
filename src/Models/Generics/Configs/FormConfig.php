<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Configs;

use Adminx\Common\Models\Generics\Forms\FormRecipient;
use Adminx\Common\Models\Generics\Forms\FormSendButton;
use ArtisanLabs\GModel\Casts\AsCollectionOf;
use ArtisanLabs\GModel\GenericModel;

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
        'send_mail'              => true,
        'allow_select_recipient' => false,
        'select_recipient_title' => 'Selecione um destinatário',
        'enable_recaptcha'       => true,
        'show_title'             => true,
        'destinations'           => [],
        'recipients'             => [],
        'send_button'            => [],
    ];

    protected $casts = [
        'send_mail'              => 'bool',
        'enable_recaptcha'       => 'bool',
        'allow_select_recipient' => 'bool',
        'select_recipient_title' => 'string',
        'show_title'             => 'bool',
        'destinations'           => 'collection',
        'recipients'             => AsCollectionOf::class . ':' . FormRecipient::class,
        'mail_from_address'      => 'string',
        'mail_from_name'         => 'string',
        'mail_reply_to_address'  => 'string',
        'mail_reply_to_name'     => 'string',
        'on_sucess'              => 'string',
        'send_button_text'       => 'string',
        'send_button_attrs'      => 'collection',
        'send_button'            => FormSendButton::class,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (blank($this->attributes['recipients']) && !blank($this->attributes['destinations'])) {
            //Get Recipients from destinations (todo: remove destinations)
            $this->attributes['recipients'] = $this->destinations->filter()->map(fn($recipient) => [
                'address' => $recipient,
            ])->toArray();
        }
    }

    protected function getRenderSendButtonHtmlAttributesAttribute()
    {
        return $this->send_button_attrs->reduce(fn($carry, $value, $key) => $carry . $key . '="' . $value . '" ');
    }


    /**
     * @throws JsonException|Exception
     */
    public function get($model, $key, $value, $attributes)
    {
        return ($this->isNullable() && is_null($value)) ? null : new static($this->castRawValue($value));

    }

    /**
     * @throws JsonException|Exception
     */
    public function set($model, $key, $value, $attributes)
    {
        //Se o valor for nulo e a model atual for nullable
        if (is_null($value) && $this->isNullable()) {
            return [$key => null]; //null;
        }

        $currentAttributes = $this->castRawValue($attributes[$key] ?? []);

        $valueArray = collect($this->castRawValue($value))->filter()->toArray();

        $valueArray = array_filter_recursive($valueArray);

        //dd($attributes[$key], $currentAttributes, $value, $this->castRawValue($value), $valueArray);

        //$mergeResult = array_replace_recursive($currentAttributes, $this->castRawValue($value));
        //$mergeResult = array_replace($currentAttributes, $this->castRawValue($value));

        //return [$key => json_encode(self::make($mergeResult)->jsonSerialize())];
        return json_encode(self::make($valueArray)->jsonSerialize());
    }
}
