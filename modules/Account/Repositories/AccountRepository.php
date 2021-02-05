<?php

namespace Modules\Account\Repositories;

use Modules\Account\Contracts\{AccountContract};

use Modules\Account\Models\Account;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class AccountRepository
 * 
 * @package Module\Account\Repositories
 */
class AccountRepository extends EloquentRepository implements AccountContract
{

    /**
     * Repository constructor.
     *
     * @param \Account  $model
     */
    public function __construct(Account $model)
    {
        $this->model = $model;
    }
	
} //Class ends