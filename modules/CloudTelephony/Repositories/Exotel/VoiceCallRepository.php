<?php

namespace Modules\CloudTelephony\Repositories\Exotel;

use Modules\CloudTelephony\Contracts\{VoiceCallContract};

use Ellaisys\Exotel\ExotelCall;
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
			if (!empty($response) && $response['Call']) {
				$objReturnValue = new ExotelVoiceCallDetailsResource($response['Call']);
			} else {
				$objReturnValue = $response;
			} //End if
	        		
		} catch(Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-catch ends
		
		return $objReturnValue;
	} //Function ends
} //Class ends