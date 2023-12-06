<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Contact;

use ArtisanLabs\GModel\GenericModel;

class Email extends GenericModel
{

    protected $fillable = [
        'title',
        'address',
    ];

    protected $attributes = [
    ];

    protected $casts = [
        'title' => 'string',
        'address' => 'string',

        'uri' => 'string',
        'html' => 'string',
        'icon_html' => 'string',
        'title_html' => 'string',
    ];

    protected $appends = [
        'uri',
        'html',
        'icon_html',
        'title_html',
        'icon_title_html',
    ];

    //region ATTRIBUTES

    //region GETS


    protected function getUriAttribute(): string
    {
        return $this->address ?? false ? "mailto:{$this->address}" : '';
    }


    protected function getHtmlAttribute(){
        return $this->address ?? false ? "<a href='{$this->uri}' data-bs-toggle='tooltip' title='Email' target='_blank'>{$this->address}</a>" : '';
    }

    protected function getIconHtmlAttribute(){
        return $this->address ?? false ? "<a href='{$this->uri}' class='d-inline-flex align-items-center' data-bs-toggle='tooltip' title='Email' target='_blank'><i class=\"far fa-envelope me-2\"></i>{$this->address}</a>" : '';
    }


    protected function getTitleHtmlAttribute(){
        return $this->address ?? false ? "<a href='{$this->uri}' target='_blank' title='Email'>{$this->address}".(!empty($this->title) ? " - {$this->title}" : '')."</a>" : '';
    }

    protected function getIconTitleHtmlAttribute(){
        return $this->address ?? false ? "<a href='{$this->uri}' class='d-inline-flex align-items-center' target='_blank' title='Email'><i class=\"far fa-envelope me-2\"></i>{$this->address}".(!empty($this->title) ? " - {$this->title}" : '')."</a>" : '';
    }


    //endregion
    //endregion
}
