<?php

namespace Modules\Agency\Repositories;

use Modules\Agency\Contracts\{AgencyContract};

use Modules\Agency\Models\Agency;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class AgencyRepository
 * 
 * @package Module\Agency\Repositories
 */
class AgencyRepository extends EloquentRepository implements AgencyContract
{

    /**
     * Repository constructor.
     *
     * @param \Agency  $model
     */
    public function __construct(Agency $model)
    {
        $this->model = $model;
    }
	
} //Class ends