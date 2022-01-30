<?php

namespace Modules\CloudTelephony\Repositories\Twilio;

use Exception;
use Illuminate\Support\Facades\Log;

use Twilio\Rest\Client as TwilioClient;
use Modules\CloudTelephony\Contracts\{VoiceCallContract};
use Modules\CloudTelephony\Transformers\Twilio\Responses\VoiceCallDetailsResource as TwilioVoiceCallDetailsResource;


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
			//Create Twilio client
			$client = new TwilioClient($settings['twilio_sid'], $settings['twilio_auth_token']);
			
			//Make Twilio Call
			$response = $client->calls->create(
				$payload['to_number'], // Call this number
				$payload['from_number'], // From a valid Twilio number
				[
					'url' => 'https://twimlets.com/holdmusic?Bucket=com.twilio.music.ambient'
				]
			);

			// $response = ExotelCall::dial(
			// 	$payload['from_number'], $payload['to_number'], $payload['virtual_number'],
			// 	$settings, $callbackUrl
			// );

			Log::info($response);

			//Return transformed response
			if (!empty($response) && isset($response['sid'])) {
				$objReturnValue = new TwilioVoiceCallDetailsResource($response);

				Log::info($objReturnValue);
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