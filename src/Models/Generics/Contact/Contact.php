<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Contact;

use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\Generics\Address;
use Adminx\Common\Models\Generics\Social\ContactSocials;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;

/**
 * @property Collection|Phone[] $extra_phones
 * @property Collection|Address[] $extra_addresses
 */
class Contact extends GenericModel
{

    protected $fillable = [
        'title',

        'phone',
        'extra_phones',

        'address',
        'extra_addresses',

        'email',
        'extra_emails',

        'socials',
    ];

    protected $attributes = [
        //'address' => [],
        'extra_emails' => [],
        'extra_phones' => [],
    ];

    protected $appends = [
        //'html_email_link',
        //'html_short_phone_links'
    ];

    protected $casts = [
        'phone' => Phone::class,
        'extra_phones' => AsCollectionOf::class.':'.Phone::class,

        'address' => Address::class,
        'extra_addresses' => AsCollectionOf::class.':'.Address::class,


        'email' => Email::class,
        'extra_emails' => AsCollectionOf::class.':'.Email::class,

        'socials' => ContactSocials::class,
    ];

    //region ATTRIBUTES
    //region GETS
    /*protected function getHtmlEmailLinkAttribute(){
        return "<a href='mailto:{$this->email}' target='_blank'>{$this->email}</a>";
    }*/

    protected function getPhonesHtmlAttribute(){
        return collect([$this->phone->html])->merge($this->extra_phones->pluck('html'))->implode('');
    }

    protected function getPhonesIconsHtmlAttribute(){
        return collect([$this->phone->icon_html])->merge($this->extra_phones->pluck('icon_html'))->implode('');
    }
    //endregion
    //endregion
}
