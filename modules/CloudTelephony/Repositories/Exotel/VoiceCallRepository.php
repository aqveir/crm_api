<?php

namespace Modules\CloudTelephony\Repositories\Exotel;

use Exception;
use Illuminate\Support\Facades\Log;

use Ellaisys\Exotel\ExotelCall;
use Modules\CloudTelephony\Contracts\{VoiceCallContract};
use Modules\CloudTelephony\Transformers\Exotel\Responses\VoiceCallDetailsResource as ExotelVoiceCallDetailsResource;


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
    public function __construct()
    {
    }


	/**
	 * Call To Connect Two Numbers
	 */
	public function makeCallToConnectTwoNumbers(array $payload, array $settings, string $callbackUrl=null)
	{
		$objReturnValue=null;
		
		try {
			//Make Exotel Call
			$response = ExotelCall::dial(
				$payload['from_number'], $payload['to_number'], $payload['virtual_number'],
				$settings, $callbackUrl
			);

			//Return transformed response
			if (!empty($response) && isset($response['Call'])) {
				$objReturnValue = new ExotelVoiceCallDetailsResource($response['Call']);
			} else if (!empty($response) && isset($response['RestException'])) {
				throw new Exception($response['RestException']['Message']);
			} else {
				$objReturnValue = $response;
			} //End if
	        		
		} catch(Exception $e) {
			Log::error('VoiceCallRepository:makeCallToConnectTwoNumbers:Exception:' . $e->getMessage());
			$objReturnValue=null;
			throw $e;
		} //Try-catch ends
		
		return $objReturnValue;
	} //Function ends
} //Class ends