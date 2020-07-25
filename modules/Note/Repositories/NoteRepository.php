<?php

namespace Modules\Note\Repositories;

use Modules\Note\Contracts\{NoteContract};

use Modules\Note\Models\Note;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class NoteRepository
 * 
 * @package Module\Note\Repositories
 */
class NoteRepository extends EloquentRepository implements NoteContract
{

    /**
     * Repository constructor.
     *
     * @param \Note  $model
     */
    public function __construct(Note $model)
    {
        $this->model = $model;
    }


	/**
	 * Get Latest/Recent Note Object
	 */
	public function getRecentNote(int $typeId, int $referenceId)
	{
		$objReturnValue=null;
		
		try {
            $query = $this->model;
	        $query = $query->where('entity_type_id', $typeId);
	        $query = $query->where('reference_id', $referenceId);
	        $query = $query->orderBy('created_at', 'desc');
	        $query = $query->firstOrFail();

	        $objReturnValue = $query;		
		} catch(Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-catch ends
		
		return $objReturnValue;
	} //Function ends
	
} //Class ends