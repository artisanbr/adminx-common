<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits;

use ArtisanBR\Adminx\Common\App\Facades\FrontendSiteEngine;
use ArtisanBR\Adminx\Common\App\Models\Bases\EloquentModelBase;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Facades\Auth;

/**
 * @var EloquentModelBase|GenericModel $this
 */
trait HasOwners
{

    //region GETS
    public function defineOwners(): void
    {

        foreach ($this->ownerTypes as $oType) {

            $oType_key = "{$oType}_id";

            if (!$this->{$oType_key}) {

                switch ($oType) {
                    case 'account':
                        $this->account_id = Auth::user()->site->account_id ?? null;
                        break;
                    case 'user':
                        $this->user_id = Auth::user()->id ?? null;
                        break;
                    case 'site':
                        $this->site_id = Auth::user()->site_id ?? FrontendSiteEngine::current()->id ?? null;
                        break;
                    default:
                        $this->{$oType_key} = Auth::user()->{$oType_key} ?? null;
                        break;
                }

            }
        }
    }
}
