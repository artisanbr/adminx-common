<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Http\Controllers;

use Adminx\Common\Models\Sites\Site;
use Adminx\Common\Models\Users\User;
use App\Http\Controllers\Controller;
use ArtisanLabs\Breadcrumbs\Providers\Breadcrumbs;
use Illuminate\Support\Facades\Auth;

abstract class ControllerBase extends Controller
{
    protected Site|null $site = null;
    protected User|null $user = null;

    /*public function __construct()
    {
        $this->user = new User();
        $this->site = new Site();

        $this->middleware(function ($request, $next) {

            if (Auth::check()) {
                $this->loadAuth();
            }

            return $next($request);
        });

    }*/

    protected bool $siteBreadcrumbs = true;

    protected function loadAuth()
    {
        if ((!$this->user || !$this->site) && Auth::user()) {
            $this->user = Auth::user();
            $this->site = Auth::user()->site;
        }
    }

    protected function breadcrumb(){
        $this->loadAuth();

        $breadcrumbs = $this->siteBreadcrumbs ?  Breadcrumbs::make($this->site->title, route('app.sites.config')) : Breadcrumbs::make();

        foreach ($this->breadcrumbDefaults() as $title => $route) {
            //dump(!is_int($title) ? $title : $route, !is_int($title) ? $route : null);
            $breadcrumbs->add(!is_int($title) ? $title : $route, !is_int($title) ? $route : null);
        }

        return $breadcrumbs;
    }

    protected function breadcrumbDefaults(){
        return [];
    }

}
