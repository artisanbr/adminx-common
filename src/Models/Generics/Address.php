<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics;

use ArtisanLabs\GModel\GenericModel;
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
        'geo_city',
        'address_number',
        'address_number_html',
        'address_simple',
        'address_simple_html',
        'address_ext',
        'address_ext_html',
        'full_address',
        'full_address_html',
        'map_uri',
        'map_uri_embed',*/
    ];

    protected $casts = [
        /*'geo_pais' => GeoPais::class,
        'geo_estado' => GeoEstado::class,
        'geo_cidade' => GeoCidade::class,*/
    ];

    //region HELPERS
    //todo: attributes
    public function getHtml($address = null): string
    {
        $address = $address ?? $this->getAddressExtAttribute();
        $mapUri = $this->getMapUriAttribute();

        return $address ? <<<blade
                        <a rel="tooltip" class="text-wrap" title="Abrir no Google Maps" href="{$mapUri}" target="_blank">
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

   /* public function set($model, $key, $value, $attributes)

    {

        //Se o valor for nulo e a model atual for nullable

        if (is_null($value) && $this->isNullable()) {

            return [$key => null]; //null;

        }



        $currentAttributes = $this->castRawValue($attributes[$key] ?? []);



        $mergeResult = array_replace_recursive($currentAttributes, $this->castRawValue($value));

        //$mergeResult = array_replace($currentAttributes, $this->castRawValue($value));



        //return [$key => json_encode(self::make($mergeResult)->jsonSerialize())];
        dump(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5));
        return json_encode(self::make($mergeResult)->jsonSerialize());

    }

    public function jsonSerialize(): array
    {
        $attributes = $this->getArrayableAttributes();


        //

        $mutatedAttributes = $this->getMutatedAttributes();
        $appendAttributes = $this->getArrayableAppends();


        // We want to spin through all the mutated attributes for this model and call
        // the mutator for the attribute. We cache off every mutated attributes so
        // we don't have to constantly check on attributes that actually change.
        foreach ($mutatedAttributes as $attribute => $value) {
            if (!array_key_exists($attribute, $attributes)) {
                continue;
            }

            $attributes[$attribute] = $this->mutateAttributeForArray(
                $attribute, $value
            );
        }

        // Next we will handle any casts that have been setup for this model and cast
        // the values to their appropriate type. If the attribute has a mutator we
        // will not perform the cast on those attributes to avoid any confusion.
        $attributes = $this->addCastAttributesToArray(
            $attributes, $mutatedAttributes
        );

        // Here we will grab all of the appended, calculated attributes to this model
        // as these attributes are not really in the attributes array, but are run
        // when we need to array or JSON the model for convenience to the coder.

        foreach ($attributes as $attribute => $value) {
            $attributes[$attribute] = $this->mutateAttributeForArray($attribute, $value);
        }


        return collect($attributes)->except($this->appends)->except($this->temporary)->toArray();

        return $attributes;
    }*/

    protected function getZipCodeNumberAttribute(): array|string|null
    {
        return preg_replace('/\D+/mi', '', $this->attributes['zip_code'] ?? '');
    }

    protected function getMapUriAttribute(): string
    {
        return '';
        return 'https://www.google.com/maps/search/' . urlencode($this->getFullAddressAttribute());
    }

    protected function getMapUriEmbedAttribute(): string
    {
        return '';
        return 'https://www.google.com/maps/embed/v1/search?key=AIzaSyCDjsl_WAFbY7D-0Scr3TX_s6dSmxVK2RA&region=br&q=' . urlencode($this->getFullAddressAttribute());
    }

    protected function getAddressNumberAttribute(): string
    {
        return ($this->address ?? '') . (($this->number ?? false) ? ", {$this->number}" : "");
    }

    protected function getAddressNumberHtmlAttribute(): string
    {
        return $this->getHtml($this->getAddressNumberAttribute());
    }

    protected function getAddressSimpleAttribute(): string
    {
        return ($this->address_number ?? '') .
            (($this->district ?? false) ? " - {$this->district}" : "");
    }

    protected function getAddressSimpleHtmlAttribute(): string
    {
        return $this->getHtml($this->getAddressSimpleAttribute());
    }

    protected function getAddressExtAttribute(): string
    {
        return $this->getAddressSimpleAttribute() . (($this->zip_code ?? false) ? " - CEP: {$this->zip_code}" : "");
    }

    protected function getAddressExtHtmlAttribute(): string
    {
        return $this->getHtml($this->getAddressExtAttribute());
    }

    protected function getFullAddressAttribute(): string
    {
        return ($this->address_number ?? '') .
            (($this->district ?? false) ? " - {$this->district}" : "") .
            (($this->city ?? false) ? " - {$this->city}, {$this->state}" : "") .
            (($this->country ?? false) ? " - {$this->country}" : "") .
            (($this->zip_code ?? false) ? " - CEP: {$this->zip_code}" : "");
    }

    protected function getFullAddressHtmlAttribute(): string
    {
        return $this->getHtml($this->getFullAddressAttribute());
    }
    //endregion
}
