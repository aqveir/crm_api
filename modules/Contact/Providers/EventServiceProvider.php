<?php

namespace Modules\Contact\Providers;

use Modules\Contact\Events\ContactUploadedEvent;
use Modules\Contact\Events\ContactBulkDataEvent;

use Modules\Contact\Listeners\ContactUploadListener;
use Modules\Contact\Listeners\ContactBulkDataListener;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //Contact File Upload Event
        ContactUploadedEvent::class => [
            ContactUploadListener::class
        ],

        //Contact Bulk Data Event
        ContactBulkDataEvent::class => [
            ContactBulkDataListener::class
        ],
    ];


    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        //
    ];
    
} //Class ends