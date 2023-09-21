<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;


trait HasWhereUrlScope
{

    /*protected array $urlAttributes = ['slug', 'public_id'];

    protected bool $usePrimaryKeyOnUrl = true;*/

    public function scopeWhereUrl(Builder $query, ?string $url = null, string|array $urlAttributes = ['slug', 'public_id'], bool $usePrimaryKeyOnUrl = true)
    {

        $urlAttributes = Collection::wrap($urlAttributes)->toArray();

        return $query->where(function (Builder $q) use ($usePrimaryKeyOnUrl, $url, $urlAttributes) {

            $q->when($usePrimaryKeyOnUrl && $this->primaryKey, function (Builder $qPK) use ($url, $urlAttributes, $usePrimaryKeyOnUrl) {
                $qPK->where($this->primaryKey, $url);
            });

            //$i = 0;
            foreach ($urlAttributes as $urlAttribute) {
                /*if (!$i) {
                    if ($url){
                        $q->where($url_attribute, $url);
                    }else{
                        $q->whereNot($url_attribute);
                    }
                }
                else*/

                if ($url){
                    $q->orWhere($urlAttribute, $url);
                }else{
                    $q->orWhereNot($urlAttribute);
                }

                //$i++;
            }

        });
    }

}
