<?php

namespace Modules\CloudTelephony\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Models\Organization\Organization;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;

use Modules\Core\Services\BaseService;

// use Modules\Note\Events\NoteCreatedEvent;
// use Modules\Note\Events\NoteUpdatedEvent;
// use Modules\Note\Events\NoteDeletedEvent;

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
     * @var \Modules\Note\Repositories\NoteRepository
     */
    protected $noteRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \Modules\Note\Repositories\NoteRepository                         $noteRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        LookupValueRepository           $lookupRepository
        //NoteRepository                  $noteRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->lookupRepository         = $lookupRepository;
        //$this->noteRepository           = $noteRepository;
    } //Function ends


    /**
     * Make a Telephony Call
     * 
     * @param \string $orgHash
     * @param \string $provider
     * @param \Illuminate\Support\Collection $payload
     *
     * @return mixed
     */
    public function makecall(string $orgHash, Collection $payload)
    {
        $objReturnValue=null; $data=[];
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Configurations
            $configurations = $organization->configurations();
            $telephonyProviders = $configurations->getByType('configuration_telephony_providers');

            if ($telephonyProviders=='configuration_telephony_providers_none') {
                throw new TelephonyNoProviderException();
            } //End if



            // //Lookup data
            // $entityType = $payload['entity_type'];
            // $lookupEntity = $this->lookupRepository->getLookUpByKey($data['org_id'], $entityType);
            // if (empty($lookupEntity))
            // {
            //     throw new Exception('Unable to resolve the entity type');   
            // } //End if
            // $data['entity_type_id'] = $lookupEntity['id'];

            // //Create Note
            // $note = $this->noteRepository->create($data);
            // $note->load('type', 'owner');
                
            //Raise event
            $this->raiseEvent($organization, $payload);

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
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Save Telephony Callback
     * 
     * @param \string $orgHash
     * @param \string $provider
     * @param \Illuminate\Support\Collection $payload
     *
     * @return mixed
     */
    public function callback(string $orgHash, string $provider, Collection $payload)
    {
        $objReturnValue=null; $data=[];
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            // //Lookup data
            // $entityType = $payload['entity_type'];
            // $lookupEntity = $this->lookupRepository->getLookUpByKey($data['org_id'], $entityType);
            // if (empty($lookupEntity))
            // {
            //     throw new Exception('Unable to resolve the entity type');   
            // } //End if
            // $data['entity_type_id'] = $lookupEntity['id'];

            // //Create Note
            // $note = $this->noteRepository->create($data);
            // $note->load('type', 'owner');
                
            //Raise event
            $this->raiseEvent($organization, $payload);

            //Assign to the return value
            $objReturnValue = $payload;

        } catch(AccessDeniedHttpException $e) {
            log::error('TelephonyVoiceService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('TelephonyVoiceService:create:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('TelephonyVoiceService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Save Telephony Details
     * 
     * @param \string $orgHash
     * @param \string $provider
     * @param \Illuminate\Support\Collection $payload
     *
     * @return mixed
     */
    public function details(string $orgHash, string $provider, Collection $payload)
    {
        $objReturnValue=null;
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Raise event
            $this->raiseEvent($organization, $payload);               

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
    private function raiseEvent(Organization $organization, Collection $payload)
    {
        $objReturnValue=null;
        try {

            switch ($payload['status']) {
                case 'telephony_call_status_type_completed':
                    event(new TelephonyCallCompleted($organization, $payload));
                    break;
                
                default:
                    //Do nothing
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