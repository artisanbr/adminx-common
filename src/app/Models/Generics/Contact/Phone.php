<?php
namespace ArtisanBR\Adminx\Common\App\Models\Generics\Contact;

use ArtisanBR\Adminx\Common\App\Libs\Support\Str;
use ArtisanLabs\GModel\GenericModel;

class Phone extends GenericModel
{

    protected $fillable = [
        'title',
        'number',
        'is_whatsapp',
        'whatsapp_message',
    ];

    protected $attributes = [
        'is_whatsapp' => false,
    ];

    protected $casts = [
        'is_whatsapp' => 'bool',
        'number' => 'string',
        'title' => 'string',
        //Appends
        'number_only' => 'int',
        'full_number_only' => 'int',
        'full_number' => 'string',

        'uri' => 'string',
        'whatsapp_uri' => 'string',
        'phone_uri' => 'string',

        'html' => 'string',
        'whatsapp_html' => 'string',
        'phone_html' => 'string',

        'icon_html' => 'string',
        'whatsapp_icon_html' => 'string',
        'phone_icon_html' => 'string',

        'title_html' => 'string',
        'title_whatsapp_html' => 'string',
        'title_phone_html' => 'string',

        'icon' => 'string',
    ];

    protected $appends = [
        'number_only',
        'full_number_only',
        'full_number',

        'whatsapp_uri',
        'phone_uri',
        'uri',

        'icon',
        'whatsapp_icon',
        'phone_icon',

        'html',
        'whatsapp_html',
        'phone_html',

        'icon_html',
        'whatsapp_icon_html',
        'phone_icon_html',

        'title_html',
        'title_whatsapp_html',
        'title_phone_html',

        'title_icon_html',
        'title_whatsapp_icon_html',
        'title_phone_icon_html',
    ];

    protected function getWhatsappUri($message = false): string
    {
        return "https://wa.me/{$this->full_number_only}" . ($message ? "?text=".urlencode($message) : '');
    }

    //region ATTRIBUTES

    //region GETS

    protected function getNumberOnlyAttribute(): ?string
    {
        return preg_replace('/\D/m', '', $this->number ?? '');
    }

    protected function getFullNumberOnlyAttribute(): string
    {
        return (!Str::contains($this->number ?? '', '+') ? '55' : '') . $this->number_only;
    }

    protected function getFullNumberAttribute(): string
    {
        return (!Str::contains($this->number ?? '', '+') ? '+55' : '') . $this->number;
    }


    protected function getWhatsappUriAttribute(): string
    {
        return $this->getWhatsappUri();
    }

    protected function getPhoneUriAttribute(): string
    {
        return "tel://{$this->number_only}";
    }

    protected function getUriAttribute(){
        return $this->is_whatsapp ? $this->whatsapp_uri : $this->phone_uri;
    }


    protected function getIconAttribute(): string
    {
        return $this->is_whatsapp ? $this->whatsapp_icon : $this->phone_icon;
    }

    protected function getPhoneIconAttribute(): string
    {
        return 'fa-solid fa-phone';
    }

    protected function getWhatsappIconAttribute(): string
    {
        return 'fa-brands fa-whatsapp';
    }



    protected function getHtmlAttribute(){
        return $this->is_whatsapp ? $this->whatsapp_html : $this->phone_html;
    }

    protected function getPhoneHtmlAttribute(){
        return $this->number ?? false ? "<a href='{$this->phone_uri}' data-bs-toggle='tooltip' title='Ligar para o Número' target='_blank'>{$this->number}</a>" : '';
    }

    protected function getWhatsappHtmlAttribute(){
        return $this->number ?? false ? "<a href=\"{$this->whatsapp_uri}\" data-bs-toggle=\"tooltip\" title=\"Abrir WhatsApp\" target=\"_blank\">{$this->number}</a>" : '';
    }


    protected function getIconHtmlAttribute(){
        return $this->is_whatsapp ? $this->whatsapp_icon_html : $this->phone_icon_html;
    }

    protected function getPhoneIconHtmlAttribute(){
        return $this->number ?? false ? "<a href='{$this->phone_uri}' class='d-inline-flex align-items-center' data-bs-toggle='tooltip' title='Ligar para o Número' target='_blank'><i class=\"{$this->phone_icon} me-2\"></i>{$this->number}</a>" : '';
    }

    protected function getWhatsappIconHtmlAttribute(){
        return $this->number ?? false ? "<a href=\"{$this->whatsapp_uri}\" class='d-inline-flex align-items-center' data-bs-toggle=\"tooltip\" title=\"Abrir WhatsApp\" target=\"_blank\"><i class=\"{$this->whatsapp_icon} me-2\"></i>{$this->number}</a>" : '';
    }

    protected function getTitleHtmlAttribute(){
        return $this->is_whatsapp ? $this->title_whatsapp_html : $this->title_phone_html;
    }

    protected function getTitlePhoneHtmlAttribute(){
        return $this->number ?? false ? "<a href='{$this->phone_uri}' target='_blank' title='Ligar para o Número'>{$this->number}".(!empty($this->title) ? " - {$this->title}" : '')."</a>" : '';
    }

    protected function getTitleWhatsappHtmlAttribute(){
        return $this->number ?? false ? "<a href='{$this->whatsapp_uri}' target='_blank' title='Abrir WhatsApp'>{$this->number}".(!empty($this->title) ? " - {$this->title}" : '')."</a>" : '';
    }

    protected function getTitleIconHtmlAttribute(){
        return $this->is_whatsapp ? $this->title_whatsapp_icon_html : $this->title_phone_icon_html;
    }

    protected function getTitlePhoneIconHtmlAttribute(){
        return $this->number ?? false ? "<a href='{$this->phone_uri}' class='d-inline-flex align-items-center' target='_blank' title='Ligar para o Número'><i class=\"{$this->phone_icon} me-2\"></i>{$this->number}".(!empty($this->title) ? " - {$this->title}" : '')."</a>" : '';
    }

    protected function getTitleWhatsappIconHtmlAttribute(){
        return $this->number ?? false ? "<a href='{$this->whatsapp_uri}' class='d-inline-flex align-items-center' target='_blank' title='Abrir WhatsApp'><i class=\"{$this->whatsapp_icon} me-2\"></i>{$this->number}".(!empty($this->title) ? " - {$this->title}" : '')."</a>" : '';
    }
    //endregion
    //endregion
}
