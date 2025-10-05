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

			Log::info($payload);
			Log::info($settings);
			Log::info($callbackUrl);

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

			// $response = {
			// 	"account_sid": "ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
			// 	"answered_by": null,
			// 	"api_version": "2010-04-01",
			// 	"caller_name": null,
			// 	"date_created": "Tue, 31 Aug 2010 20:36:28 +0000",
			// 	"date_updated": "Tue, 31 Aug 2010 20:36:44 +0000",
			// 	"direction": "inbound",
			// 	"duration": "15",
			// 	"end_time": "Tue, 31 Aug 2010 20:36:44 +0000",
			// 	"forwarded_from": "+141586753093",
			// 	"from": "+15017122661",
			// 	"from_formatted": "(501) 712-2661",
			// 	"group_sid": null,
			// 	"parent_call_sid": null,
			// 	"phone_number_sid": "PNXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
			// 	"price": "-0.03000",
			// 	"price_unit": "USD",
			// 	"sid": "CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
			// 	"start_time": "Tue, 31 Aug 2010 20:36:29 +0000",
			// 	"status": "completed",
			// 	"subresource_uris": {
			// 	  "notifications": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Notifications.json",
			// 	  "recordings": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Recordings.json",
			// 	  "feedback": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Feedback.json",
			// 	  "feedback_summaries": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/FeedbackSummary.json",
			// 	  "payments": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Payments.json",
			// 	  "events": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Events.json",
			// 	  "siprec": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Siprec.json",
			// 	  "streams": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Streams.json",
			// 	  "user_defined_message_subscriptions": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/UserDefinedMessageSubscriptions.json",
			// 	  "user_defined_messages": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/UserDefinedMessages.json"
			// 	},
			// 	"to": "+14155551212",
			// 	"to_formatted": "(415) 555-1212",
			// 	"trunk_sid": null,
			// 	"uri": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX.json",
			// 	"queue_time": "1000"
			//   };

			
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