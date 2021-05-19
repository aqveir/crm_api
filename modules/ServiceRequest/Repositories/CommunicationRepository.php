<?php

namespace Modules\ServiceRequest\Repositories;

use Modules\ServiceRequest\Contracts\{CommunicationContract};

use Modules\ServiceRequest\Models\Communication;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class CommunicationRepository
 * 
 * @package Module\ServiceRequest\Repositories
 */
class CommunicationRepository extends EloquentRepository implements CommunicationContract
{

    /**
     * Repository constructor.
     *
     * @param \Communication  $model
     */
    public function __construct(Communication $model)
    {
        $this->model = $model;
    }

} //Class ends