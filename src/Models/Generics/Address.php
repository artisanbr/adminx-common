<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics;

use ArtisanLabs\LaravelGeoDatabase\Models\GeoCity;
use ArtisanLabs\LaravelGeoDatabase\Models\GeoCountry;
use ArtisanLabs\LaravelGeoDatabase\Models\GeoState;

class Address extends GenericModel
{

    protected $fillable = [
        'title',
        'zip_code',
        'address',
        'number',
        'complement',
        'district',
        'city',
        'city_id',
        'state',
        'state_id',
        'country',
        'country_id',
    ];

    protected $attributes = [
        'country' => 'Brasil',
    ];

    protected $appends = [
        /*'geo_country',
        'geo_state',
        'geo_city',*/
        'address_number',
        'address_number_html',
        'address_simple',
        'address_simple_html',
        'address_ext',
        'address_ext_html',
        'full_address',
        'full_address_html',
        'map_uri',
        'map_uri_embed',
    ];

    protected $casts = [
        /*'geo_pais' => GeoPais::class,
        'geo_estado' => GeoEstado::class,
        'geo_cidade' => GeoCidade::class,*/
    ];

    //region HELPERS
    //todo: attributes
    public function getHtml($address = null)
    {
        $address = $address ?? $this->address_ext;

        return $address ? <<<blade
                        <a rel="tooltip" class="text-wrap" title="Abrir no Google Maps" href="{$this->map_uri}" target="_blank">
                            {$address}
                        </a>
                        blade: "-";
    }
    //endregion

    //region ATTRIBUTES

    //region GEO
    protected function getGeoCountryAttribute(): GeoCountry
    {
        return GeoCountry::whereId($this->attributes['country_id'] ?? null)->orWhere('title', $this->attributes['country'] ?? null)->firstOrNew();
    }

    protected function getGeoStateAttribute(): GeoState
    {
        return GeoState::whereId($this->attributes['state_id'] ?? null)->orWhere('slug', $this->attributes['state'] ?? null)->firstOrNew();
    }

    protected function getGeoCityAttribute(): GeoCity
    {
        return GeoCity::whereId($this->attributes['city_id'] ?? null)->orWhere('title', $this->attributes['city'] ?? null)->firstOrNew();
    }

    //endregion

    protected function getZipCodeNumberAttribute()
    {
        return preg_replace('/\D+/mi', '', $this->zip_code);
    }

    protected function getMapUriAttribute()
    {
        return 'https://www.google.com/maps/search/' . urlencode($this->full_address);
    }

    protected function getMapUriEmbedAttribute()
    {
        return 'https://www.google.com/maps/embed/v1/search?key=AIzaSyCDjsl_WAFbY7D-0Scr3TX_s6dSmxVK2RA&region=br&q=' . urlencode($this->full_address);
    }

    protected function getAddressNumberAttribute(): string
    {
        return $this->address . ($this->number ? ", {$this->number}" : "");
    }

    protected function getAddressNumberHtmlAttribute(): string
    {
        return $this->getHtml($this->address_number);
    }

    protected function getAddressSimpleAttribute(): string
    {
        return $this->address_number .
            ($this->district ? " - {$this->district}" : "");
    }

    protected function getAddressSimpleHtmlAttribute(): string
    {
        return $this->getHtml($this->address_simple);
    }

    protected function getAddressExtAttribute(): string
    {
        return $this->address_simple . ($this->zip_code ? " - CEP: {$this->zip_code}" : "");
    }

    protected function getAddressExtHtmlAttribute(): string
    {
        return $this->getHtml($this->address_ext);
    }

    protected function getFullAddressAttribute(): string
    {
        return $this->address_number .
            ($this->district ? " - {$this->district}" : "") .
            ($this->city ? " - {$this->city}, {$this->state}" : "") .
            ($this->country ? " - {$this->country}" : "") .
            ($this->zip_code ? " - CEP: {$this->zip_code}" : "");
    }

    protected function getFullAddressHtmlAttribute()
    {
        return $this->getHtml($this->full_address);
    }
    //endregion
}
