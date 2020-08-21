<?php

namespace Modules\User\Repositories\User;

use Modules\User\Models\User\UserAvailabilityHistory;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class UserAvailabilityHistoryRepository
 * @package Modules\User\Repositories\User
 */
class UserAvailabilityHistoryRepository extends EloquentRepository
{

    /**
     * Repository constructor.
     *
     * @param  UserAvailabilityHistory  $model
     */
    public function __construct(UserAvailabilityHistory $model)
    {
        $this->model = $model;
    }

} //Class ends
