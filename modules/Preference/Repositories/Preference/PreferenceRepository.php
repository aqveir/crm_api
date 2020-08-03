<?php

namespace Modules\Preference\Repositories\Preference;

use Modules\Preference\Contracts\{PreferenceContract};

use Modules\Preference\Models\Preference\Preference;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class PreferenceRepository
 * @package Modules\Preference\Repositories\Preference
 */
class PreferenceRepository extends EloquentRepository implements PreferenceContract
{

    /**
     * Repository constructor.
     *
     * @param  Preference  $model
     */
    public function __construct(Preference $model)
    {
        $this->model = $model;
    }

} //Class ends
