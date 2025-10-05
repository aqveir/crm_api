<?php

namespace Modules\CloudTelephony\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Models\Organization\Organization;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\CloudTelephony\Repositories\Exotel\VoiceCallRepository as ExotelVoiceCallRepository;
use Modules\CloudTelephony\Repositories\Twilio\VoiceCallRepository as TwilioVoiceCallRepository;

use Modules\Core\Services\BaseService;

use Modules\CloudTelephony\Events\Call\TelephonyCallCallbackReceivedEvent;
use Modules\CloudTelephony\Events\Call\TelephonyCallInProgressEvent;
use Modules\CloudTelephony\Events\Call\TelephonyCallCompletedEvent;
use Modules\CloudTelephony\Events\Call\TelephonyCallNotConnectedEvent;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Modules\CloudTelephony\Exceptions\TelephonyNoProviderException;
use Modules\CloudTelephony\Exceptions\TelephonyConfigurationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class TelephonyVoiceService
 * @package Modules\CloudTelephony\Services
 */
class TelephonyVoiceService extends BaseService
{

    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var Modules\Core\Repositories\Lookup\LookupValueRepository
     */
    protected $lookupRepository;


    /**
     * @var \VoiceCallRepository
     */
    protected $exotelVoiceCallRepository;
    protected $twilioVoiceCallRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \ExotelVoiceCallRepository                                        $exotelVoiceCallRepository
     * @param \TwilioVoiceCallRepository                                        $twilioVoiceCallRepository
     * 
     */
    public function __construct(
        OrganizationRepository              $organizationRepository,
        LookupValueRepository               $lookupRepository,
        ExotelVoiceCallRepository           $exotelVoiceCallRepository,
        TwilioVoiceCallRepository           $twilioVoiceCallRepository
    ) {
        $this->organizationRepository       = $organizationRepository;
        $this->lookupRepository             = $lookupRepository;
        $this->exotelVoiceCallRepository    = $exotelVoiceCallRepository;
        $this->twilioVoiceCallRepository    = $twilioVoiceCallRepository;
    } //Function ends


    /**
     * Make a Telephony Call - Outgoing
     * 
     * @param \string $orgHash
     * @param \string $provider
     * @param \Illuminate\Support\Collection $payload
     *
     * @return mixed
     */
    public function makeCall(string $orgHash, Collection $payload)
    {
        $objReturnValue=null; $data=[]; $response=null; $callbackUrl=null;
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Load Telephony Provider from Configuration
            $configuration = $organization->getOrganizationConfigurationByKey('configuration_telephony_call_providers');
            if (empty($configuration) || empty($configuration['pivot']['value'])) {
                throw new TelephonyNoProviderException();
            } //End if

            //Convert payload to Array
            $payload = $payload->toArray();

            //Check Telephony Providers
            $telephonyProvider = $configuration['pivot']['value'];

            //Get Exotel Outgoing Number
            $virtualNumber = $organization->getOrganizationConfigurationByKey('configuration_telephony_outgoing_phone_number');
            if (empty($virtualNumber) || empty($virtualNumber['pivot']['value'])) {
                throw new TelephonyConfigurationException();
            } //End if
            $payload['virtual_number'] = json_encode(($virtualNumber['pivot']['value']), true);

            switch ($telephonyProvider) {
                case 'configuration_telephony_providers_exotel': //Exotel
                    //Load configuration
                    $configuration = $organization->getOrganizationConfigurationByKey('configuration_telephony_exotel');
                    if (empty($configuration) || empty($configuration['pivot']['value'])) {
                        throw new TelephonyConfigurationException();
                    } //End if
                    $settings = json_decode(($configuration['pivot']['value']), true);

                    //Callback URL
                    $callbackUrl = url(config('cloudtelephony.exotel.call.callback-url'));
                    $callbackUrl .= "?key=" . $orgHash;
                    $callbackUrl .= "&sr_hash=" . $srHash;
                    
                    $response = $this->exotelVoiceCallRepository->makeCallToConnectTwoNumbers($payload, $settings, $callbackUrl);
                    break;

                case 'configuration_telephony_providers_twilio': //Twilio
                    //Load configuration
                    $configuration = $organization->getOrganizationConfigurationByKey('configuration_telephony_twilio');
                    if (empty($configuration) || empty($configuration['pivot']['value'])) {
                        throw new TelephonyConfigurationException();
                    } //End if
                    $settings = json_decode(($configuration['pivot']['value']), true);

                    //Callback URL
                    $callbackUrl = url(config('cloudtelephony.twilio.call.callback-url'));
                    $callbackUrl .= "?key=" . $orgHash;

                    $response = $this->twilioVoiceCallRepository->makeCallToConnectTwoNumbers($payload, $settings, $callbackUrl);
                    break;
                
                default:
                    throw new TelephonyNoProviderException();
                    break;
            } //End switch

            //Check for response
            if (empty($response)) {
                throw new Exception('Telephony Response Error');
            } //End if
                
            //Raise event
            $this->raiseEvent($organization, collect($responnse));

            //Assign to the return value
            $objReturnValue = $payload;

        } catch(TelephonyNoProviderException $e) {
            log::error('TelephonyVoiceService:makecall:TelephonyNoProviderException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(TelephonyConfigurationException $e) {
            log::error('TelephonyVoiceService:makecall:TelephonyConfigurationException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('TelephonyVoiceService:makecall:Exception:' . $e->getMessage());
            throw new HttpException(500, $e->getMessage());
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Save Telephony Callback
     * 
     * @param \string $orgHash
     * @param \string $provider
     * @param \Illuminate\Support\Collection $payload
     * @param \string $ipAddress (optional)
     *
     * @return mixed
     */
    public function callback(string $orgHash, string $provider, Collection $payload, string $ipAddress=null)
    {
        $objReturnValue=null; $data=[];
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
                
            //Raise events
            event(new TelephonyCallCallbackReceivedEvent($organization, $payload, $ipAddress));
            $this->raiseEvent($organization, $payload, $ipAddress);

            //Assign to the return value
            $objReturnValue = $payload;

        } catch(AccessDeniedHttpException $e) {
            log::error('TelephonyVoiceService:callback:AccessDeniedHttpException:' . $e->getMessage());
            throw $e;
        } catch(BadRequestHttpException $e) {
            log::error('TelephonyVoiceService:callback:BadRequestHttpException:' . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            log::error('TelephonyVoiceService:callback:Exception:' . $e->getMessage());
            throw new Exception(500, $e->getMessage());
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Save Telephony Details
     * 
     * @param \string $orgHash
     * @param \string $provider
     * @param \Illuminate\Support\Collection $payload
     * @param \string $ipAddress (optional)
     *
     * @return mixed
     */
    public function details(string $orgHash, string $provider, Collection $payload, string $ipAddress=null)
    {
        $objReturnValue=null;
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Raise event
            $this->raiseEvent($organization, $payload, $ipAddress);               

            //Assign to the return value
            $objReturnValue = $payload;

        } catch(AccessDeniedHttpException $e) {
            log::error('TelephonyVoiceService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('TelephonyVoiceService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('TelephonyVoiceService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Raise Telephony Event
     * 
     * @param \Modules\Core\Models\Organization\Organization $organization
     * @param \Illuminate\Support\Collection $payload
     *
     * @return mixed
     */
    private function raiseEvent(Organization $organization, Collection $payload, string $ipAddress=null)
    {
        $objReturnValue=null;
        try {

            switch ($payload['call_status_key']) {
                case 'telephony_call_status_type_queued':
                case 'telephony_call_status_type_in_progress':
                    event(new TelephonyCallInProgressEvent($organization, $payload, $ipAddress));
                    break;

                case 'telephony_call_status_type_completed':
                    event(new TelephonyCallCompletedEvent($organization, $payload, $ipAddress));
                    break;

                case 'telephony_call_status_type_failed':
                case 'telephony_call_status_type_busy':
                case 'telephony_call_status_type_no_answer':
                default:
                    event(new TelephonyCallNotConnectedEvent($organization, $payload, $ipAddress));
                    break;
            } //End switch

        } catch(AccessDeniedHttpException $e) {
            log::error('TelephonyVoiceService:raiseEvent:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('TelephonyVoiceService:raiseEvent:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('TelephonyVoiceService:raiseEvent:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends