<?php

namespace Modules\Customer\Repositories\Customer;

use Modules\Customer\Contracts\{CustomerAddressContract};

use Modules\Customer\Models\Customer\CustomerAddress;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class CustomerAddressRepository
 * @package App\Repositories\Customer
 */
class CustomerAddressRepository extends EloquentRepository
{

    /**
     * CustomerAddressRepository constructor.
     *
     * @param  Customer  $model
     */
    public function __construct(CustomerAddress $model)
    {
        $this->model = $model;
    }

} //Class ends
