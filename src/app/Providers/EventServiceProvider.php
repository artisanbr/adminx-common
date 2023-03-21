<?php

namespace ArtisanBR\Adminx\Common\App\Providers;

use ArtisanBR\Adminx\Common\App\Models\Category;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomList;
use ArtisanBR\Adminx\Common\App\Models\File;
use ArtisanBR\Adminx\Common\App\Models\Folder;
use ArtisanBR\Adminx\Common\App\Models\Page;
use ArtisanBR\Adminx\Common\App\Models\Post;
use ArtisanBR\Adminx\Common\App\Models\Report;
use ArtisanBR\Adminx\Common\App\Models\Site;
use ArtisanBR\Adminx\Common\App\Models\Theme;
use ArtisanBR\Adminx\Common\App\Models\VisitTracker\Cookie;
use ArtisanBR\Adminx\Common\App\Models\VisitTracker\Path;
use ArtisanBR\Adminx\Common\App\Models\VisitTracker\Session;
use ArtisanBR\Adminx\Common\App\Models\VisitTracker\Visit;
use ArtisanBR\Adminx\Common\App\Models\Widgeteable;
use ArtisanBR\Adminx\Common\App\Observers\HtmlModelObserver;
use ArtisanBR\Adminx\Common\App\Observers\MenuableObserver;
use ArtisanBR\Adminx\Common\App\Observers\OwneredModelObserver;
use ArtisanBR\Adminx\Common\App\Observers\PublicIdModelObserver;
use ArtisanBR\Adminx\Common\App\Observers\WidgeteableObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [

    ];

    protected $observers = [

        /** Visit Tracker */
        Session::class => [
            OwneredModelObserver::class,
        ],
        Cookie::class => [
            OwneredModelObserver::class,
        ],
        Path::class => [
            OwneredModelObserver::class,
        ],
        Visit::class => [
            OwneredModelObserver::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
