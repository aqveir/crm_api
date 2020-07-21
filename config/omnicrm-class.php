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

use Modules\User\Models\User\User;
use Modules\Customer\Models\Customer\Customer;
use Modules\Feedback\Models\Feedback\Feedback;
use Modules\Note\Models\Note\Note;
use Modules\Document\Models\Document\Document;

return [

    // Class Models Defined
    'class_model' => [
        //Common
        'country' => Country::class,
        'currency' => Currency::class,
        'timezone' => TimeZone::class,

        //Organization
        'organization' => Organization::class,

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

        //Customer Classes
        'customer' => [
            'main' => Customer::class
        ],

        //Other Functional Classes
        'feedback' => Feedback::class,
        'note' => Note::class,

        //Document classes
        'document' => Document::class,
    ],
];