<?php

use Modules\Core\Models\Common\Country;
use Modules\Core\Models\Common\Currency;
use Modules\Core\Models\Common\TimeZone;

use Modules\Core\Models\Lookup\Lookup;
use Modules\Core\Models\Lookup\LookupValue;

use Modules\Core\Models\Role\Role;
use Modules\Core\Models\Role\RolePrivilege;

use Modules\Core\Models\Privilege\Privilege;

use Modules\Core\Models\Organization\Organization;
use Modules\Account\Models\Account;

use Modules\User\Models\User\User;

use Modules\ServiceRequest\Models\ServiceRequest;

use Modules\Contact\Models\Contact\Contact;
use Modules\Feedback\Models\Feedback\Feedback;
use Modules\Note\Models\Note;
use Modules\Document\Models\Document;

return [

    // Class Models Defined
    'class_model' => [
        //Common
        'country' => Country::class,
        'currency' => Currency::class,
        'timezone' => TimeZone::class,

        //Organization
        'organization' => Organization::class,

        //Account
        'account' => Account::class,

        //Role Classes
        'role' => Role::class,
        'privilege' => Privilege::class,
        'role_privilege' => RolePrivilege::class,

        //Lookup Classes
        'lookup' => Lookup::class,
        'lookup_value' => LookupValue::class,   
        
        //User Classes
        'user' => [
            'main' => User::class,
        ],        

        //Contact Classes
        'contact' => [
            'main' => Contact::class
        ],

        //ServiceRequest Classes
        'service_request' => [
            'main' => ServiceRequest::class
        ],

        //Other Functional Classes
        'feedback' => Feedback::class,
        'note' => Note::class,

        //Document classes
        'document' => Document::class,
    ],
];