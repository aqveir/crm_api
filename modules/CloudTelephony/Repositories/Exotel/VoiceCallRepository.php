<?php

namespace Modules\CloudTelephony\Repositories\Exotel;

use Modules\CloudTelephony\Contracts\{VoiceCallContract};


/**
 * Class VoiceCallRepository.
 * 
 * @package Modules\CloudTelephony\Repositories
 */
class VoiceCallRepository implements VoiceCallContract
{

    /**
     * Repository constructor.
     *
     * @param \string  $provider
     */
    public function __construct(Note $model)
    {
        $this->model = $model;
    }


	/**
	 * Call To Connect Two Numbers
	 */
	public function makeCallToConnectTwoNumbers(int $typeId, int $referenceId)
	{
		$objReturnValue=null;
		
		try {

	        $objReturnValue = $query;		
		} catch(Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-catch ends
		
		return $objReturnValue;
	} //Function ends
} //Class ends