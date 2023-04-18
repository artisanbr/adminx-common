<?php
namespace Adminx\Common\Models\Generics\Social;

use Adminx\Common\Enums\SocialLinkType;
use Adminx\Common\Models\Generics\Address;
use Adminx\Common\Models\Generics\Contact\Phone;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;

/**
 * @property Collection|Phone[] $extra_phones
 * @property Collection|Address[] $extra_addresses
 */
class SocialNetwork extends GenericModel
{

    protected $fillable = [
        'title',
        'type',
        'uri',
    ];

    protected $attributes = [
    ];

    protected $casts = [
        'title' => 'string',
        'uri' => 'string',
        'type' => SocialLinkType::class,
    ];

    protected $appends = [
        'icon_link_html',
        'complete_link_html',
    ];

    //region ATTRIBUTES

    //region GETS
    //todo: html attributes (advanced html)
    protected function getIconLinkHtmlAttribute(): ?string
    {
        return $this->uri && $this->type ? "<a href='{$this->uri}' target='_blank'><i class='{$this->type->icon()}'></i></a>" : null;
    }
    protected function getCompleteLinkHtmlAttribute(): ?string
    {
        return $this->uri && $this->type ? "<a href='{$this->uri}' target='_blank'><i class='{$this->type->icon()} me-2'></i> {$this->type->name}</a>" : null;
    }
    //endregion
    //endregion
}
