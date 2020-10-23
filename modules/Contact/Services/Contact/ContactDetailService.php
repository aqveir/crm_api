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
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookuprepository
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationrepository
     * @param \Modules\Contact\Repositories\Contact\ContactDetailRepository  $customerdetailrepository
     */
    public function __construct(
        LookupValueRepository               $lookuprepository,
        OrganizationRepository              $organizationrepository,
        ContactDetailRepository            $customerdetailrepository
    ) {
        $this->lookuprepository             = $lookuprepository;
        $this->organizationrepository       = $organizationrepository;
        $this->customerdetailrepository     = $customerdetailrepository;
    } //Function ends


    /**
     * Contact Exists
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return bool
     */
    public function getDetailsByIdentifier(string $orgId, string $identifier, bool $isPrimary=null, bool $isActive=null)
    {
        $objReturnValue=false;
        try {
            $type_key = null;
            if (is_numeric($identifier)) {
                $type_key = config('omnichannel.settings.static.key.lookup_value.phone');
            } else {
                $type_key = config('omnichannel.settings.static.key.lookup_value.email');
            } //End if

            //Check if the Contact exists
            $response = $this->customerdetailrepository->getContactDetailsByIdentifier($orgId, $identifier, null, $isPrimary, $isActive);

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
