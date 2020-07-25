<?php

namespace Modules\Document\Repositories;

use Modules\Document\Contracts\{DocumentContract};

use Modules\Document\Models\Document;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class DocumentRepository
 * 
 * @package Modules\Document\Repositories
 */
class DocumentRepository extends EloquentRepository implements DocumentContract
{

    /**
     * Repository constructor.
     *
     * @param  Customer  $model
     */
    public function __construct(Document $model)
    {
        $this->model = $model;
    }

} //Class ends