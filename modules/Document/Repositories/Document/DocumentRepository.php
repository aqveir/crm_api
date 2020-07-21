<?php

namespace App\Repositories\Document;


use App\Contracts\Document\{DocumentContract};

use App\Models\Document\Document;
use App\Repositories\EloquentRepository;

/**
 * Class DocumentRepository
 * 
 * @package App\Repositories\Document
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