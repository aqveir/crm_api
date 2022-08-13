<?php

namespace Modules\Contact\Services\Contact;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Contact\Repositories\Contact\ContactRepository;
use Modules\Contact\Repositories\Contact\ContactDetailRepository;

use Modules\Contact\Events\ContactCreatedEvent;

use Modules\Core\Services\BaseService;
use Modules\Contact\Notifications\ContactActivationNotification;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use Modules\Contact\Events\ContactCallOutgoingEvent;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class ContactTelephonyService
 * 
 * @package Modules\Contact\Services\Contact
 */
class ContactTelephonyService extends BaseService
{
    /**
     * @var Modules\Core\Repositories\Lookup\LookupValueRepository
     */
    protected $lookuprepository;


    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationrepository;


    /**
     * @var Modules\Contact\Repositories\Contact\ContactRepository
     */
    protected $customerrepository;


    /**
     * @var Modules\Contact\Repositories\Contact\ContactDetailRepository
     */
    protected $customerdetailrepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookuprepository
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationrepository
     * @param \Modules\Contact\Repositories\Contact\ContactRepository        $customerrepository
     */
    public function __construct(
        LookupValueRepository               $lookuprepository,
        OrganizationRepository              $organizationrepository,
        ContactRepository                   $customerrepository,
        ContactDetailRepository             $customerdetailrepository
    ) {
        $this->lookuprepository             = $lookuprepository;
        $this->organizationrepository       = $organizationrepository;
        $this->customerrepository           = $customerrepository;
        $this->customerdetailrepository     = $customerdetailrepository;
    } //Function ends


    /**
     * Contact Exists
     * 
     * @param string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * 
     * @return bool
     */
    public function makeCall(string $orgHash, string $contactHash, string $proxy=null, Collection $payload, string $ipAddress=null)
    {
        $objReturnValue=false;
        try {
            //Get organization data
            $organization = $this->organizationrepository->getOrganizationByHash($orgHash);

            //Authenticated User
            $user = $this->getCurrentUser('backend');
            if ($user && empty($user['phone'])) {
                throw new ModelNotFoundException($user?'Users phone not available':'User not available');
            } //End if

            //Get Contact Data
            $contact = $this->customerrepository->getFullDataByIdentifier($user['org_id'], $contactHash);

            //Contact Details Type
            $typeKey = config('aqveir.settings.static.key.lookup_value.phone');
            $detailsType = $this->lookuprepository->getLookUpByKey($user['org_id'], $typeKey);

            //Check if the Contact exists
            $response = $this->customerdetailrepository->getContactDetailsByTypeId($user['org_id'], $contact['id'], $detailsType['id'], empty($proxy), $proxy);
            if ($response) {
                //Build payload
                $telephonyPayload = [
                    'org_hash'      => $organization['hash'],
                    'to_id'         => $contact['id'],
                    'to_name'       => $contact['full_name'],
                    'to_number'     => $response['identifier'],
                    'from_id'       => $user['id'],
                    'from_name'     => $user['full_name'],
                    'from_number'   => $user['phone']
                ];
                $payload = collect(array_merge($telephonyPayload, $payload->toArray()));

                //Raise event to initiate outgoing call
                event(new ContactCallOutgoingEvent($payload, $contact));
            } //End if

            $objReturnValue = $payload;
        } catch(ModelNotFoundException $e) {
            Log::error('ContactTelephonyService:makeCall:ModelNotFoundException:' . $e->getMessage());
        } catch (Exception $e) {
            log::error('ContactTelephonyService:makeCall:Exception:' . $e->getMessage());
            throw new HttpException(500, $e->getMessage());
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends
