<?php

namespace Modules\Contact\Repositories\Contact;

use Modules\Contact\Contracts\{ContactAddressContract};

use Modules\Contact\Models\Contact\ContactAddress;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class ContactAddressRepository
 * @package App\Repositories\Contact
 */
class ContactAddressRepository extends EloquentRepository
{

    /**
     * ContactAddressRepository constructor.
     *
     * @param  Contact  $model
     */
    public function __construct(ContactAddress $model)
    {
        $this->model = $model;
    }

} //Class ends
