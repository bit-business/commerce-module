<?php

namespace NadzorServera\Skijasi\Module\Commerce\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use NadzorServera\Skijasi\Module\Commerce\Events\GoogleAnalytics;
use NadzorServera\Skijasi\Module\Commerce\Events\OrderStateWasChanged;
use NadzorServera\Skijasi\Module\Commerce\Listeners\SendHit;
use NadzorServera\Skijasi\Module\Commerce\Listeners\SendNotificationToUser;

class SkijasiCommerceModuleEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        OrderStateWasChanged::class => [
            SendNotificationToUser::class,
        ],
        // GoogleAnalytics::class => [
        //     SendHit::class,
        // ],
    ];
}
