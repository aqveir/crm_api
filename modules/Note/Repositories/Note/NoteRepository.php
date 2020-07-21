<?php

namespace Module\Note\Repositories\Note;


use App\Contracts\Note\{NoteContract};

use App\Models\Note\Note;
use App\Repositories\EloquentRepository;

/**
 * Class NoteRepository
 * 
 * @package Module\Note\Repositories\Note
 */
class NoteRepository extends EloquentRepository implements NoteContract
{

    /**
     * Repository constructor.
     *
     * @param  Customer  $model
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
	

	/**
	 * Get the recent note text
	 */
    public function getRecentNoteText(int $orgId, $servicerequestId) {
        $strNoteText='';
        try {
            $type =  $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.service_request'));
            $note = $this->getRecentNote($type->id, $servicerequestId);
            $strNoteText=$note->note;
        } catch(Exception $e) {
            Log::error(json_encode($e));
            $strNoteText='';
        } //Try-catch ends
        
        return $strNoteText;
    } //Function ends

} //Class ends