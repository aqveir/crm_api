<?php

namespace Modules\Contact\Services\Contact;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Contact\Repositories\Contact\ContactDetailRepository;

use Modules\Core\Services\BaseService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class ContactDetailService
 * 
 * @package App\Services\Contact
 */
class ContactDetailService extends BaseService
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
     * @var Modules\Contact\Repositories\Contact\ContactDetailRepository
     */
    protected $customerdetailrepository;


    /**
     * @var \libphonenumber\PhoneNumberUtil
     */
    protected $phoneNumberUtil;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookuprepository
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationrepository
     * @param \Modules\Contact\Repositories\Contact\ContactDetailRepository  $customerdetailrepository
     */
    public function __construct(
        LookupValueRepository               $lookuprepository,
        OrganizationRepository              $organizationrepository,
        ContactDetailRepository             $customerdetailrepository
    ) {
        $this->lookuprepository             = $lookuprepository;
        $this->organizationrepository       = $organizationrepository;
        $this->customerdetailrepository     = $customerdetailrepository;
        $this->phoneNumberUtil              = \libphonenumber\PhoneNumberUtil::getInstance();
    } //Function ends


    /**
     * Get Contact Details by Identifier (i.e. Phone, Email)
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return bool
     */
    public function checkDuplicate(string $orgId, mixed $data, bool $isPrimary=null, bool $isActive=null)
    {
        $objReturnValue=false;
        try {
            if (is_array($data)) {
                foreach ($data as $detail) {
                    $typeKey=null;
                    $objPhoneNumber=null;

                    if (array_key_exists('identifier', $detail)) {
                        $identifier = $detail['identifier'];
                        if (!empty($identifier)) {

                            //Email data type
                            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) { 
                                $typeKey = config('omnichannel.settings.static.key.lookup_value.email');
                            } else {
                                $objPhoneNumber = $this->phoneNumberUtil->parse($identifier);
                            } //End if

                            //Phone data type
                            if ((!empty($objPhoneNumber)) && ($this->phoneNumberUtil->isValidNumber($objPhoneNumber))) { 
                                $typeKey = config('omnichannel.settings.static.key.lookup_value.phone');
                            } //End if
                            
                            //Get details by Identifier
                            $response = $this->getDetailsByIdentifier($orgId, $data, $isPrimary, $isActive);

                        } //End if
                    } //End if
                } //Loop ends
            } elseif(is_string($data)) {
                $response = $this->getDetailsByIdentifier($orgId, $data, $isPrimary, $isActive);
            } else {
                //Do nothing
            } //End if

            $objReturnValue = $response;
        } catch(ModelNotFoundException $e) {
            Log::error('ContactDetailService:getDetailsByIdentifier:ModelNotFoundException:' . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            log::error('ContactDetailService:getDetailsByIdentifier:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Get Contact Details by Identifier (i.e. Phone, Email)
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return bool
     */
    public function getDetailsByIdentifier(string $orgId, string $identifier, bool $isPrimary=null, bool $isActive=null)
    {
        $objReturnValue=false;
        try {
            $typeKey = null;
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $typeKey = config('omnichannel.settings.static.key.lookup_value.email');
            } else {
                $typeKey = config('omnichannel.settings.static.key.lookup_value.phone');
            } //End if

            //Check if the Contact exists
            $response = $this->customerdetailrepository->getContactDetailByIdentifier($orgId, $identifier, null, $isPrimary, $isActive);

            $objReturnValue = $response;
        } catch(ModelNotFoundException $e) {
            Log::error('ContactDetailService:getDetailsByIdentifier:ModelNotFoundException:' . $e->getMessage());
            throw new ModelNotFoundException();
        } catch (Exception $e) {
            log::error('ContactDetailService:getDetailsByIdentifier:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends
