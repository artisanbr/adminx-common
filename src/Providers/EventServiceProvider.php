<?php

namespace Adminx\Common\Providers;

use Adminx\Common\Models\Category;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\File;
use Adminx\Common\Models\Folder;
use Adminx\Common\Models\Page;
use Adminx\Common\Models\Post;
use Adminx\Common\Models\Report;
use Adminx\Common\Models\Site;
use Adminx\Common\Models\Theme;
use Adminx\Common\Models\VisitTracker\Cookie;
use Adminx\Common\Models\VisitTracker\Path;
use Adminx\Common\Models\VisitTracker\Session;
use Adminx\Common\Models\VisitTracker\Visit;
use Adminx\Common\Models\Widgeteable;
use Adminx\Common\Observers\HtmlModelObserver;
use Adminx\Common\Observers\MenuableObserver;
use Adminx\Common\Observers\OwneredModelObserver;
use Adminx\Common\Observers\PublicIdModelObserver;
use Adminx\Common\Observers\WidgeteableObserver;
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
