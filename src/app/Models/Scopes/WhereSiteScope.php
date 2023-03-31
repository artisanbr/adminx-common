<?php

namespace ArtisanBR\Adminx\Common\App\Models\Scopes;

use ArtisanBR\Adminx\Common\App\Facades\FrontendSite;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\OwneredModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class WhereSiteScope implements Scope
{
    /**
     * @param Builder      $builder
     * @var OwneredModel|Model $model
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $siteId = Auth::user()->site_id ?? FrontendSite::current()->id ?? null;
        if($siteId){
            $builder->where('site_id', $siteId);
        }
    }
}
