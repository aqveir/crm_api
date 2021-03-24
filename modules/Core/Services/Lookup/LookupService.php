<?php

namespace Modules\Core\Services\Lookup;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;

use Modules\Core\Services\BaseService;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
 * Class LookupService
 * 
 * @package Modules\Core\Services\Lookup
 */
class LookupService extends BaseService
{
    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var \Modules\Core\Repositories\Lookup\LookupRepository
     */
    protected $lookupRepository;


    /**
     * @var \Modules\Core\Repositories\Lookup\LookupValueRepository
     */
    protected $lookupvalueRepository;


    /**
     * Service constructor.
     *
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupRepository                $lookupRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupvalueRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        LookupRepository                $lookupRepository,
        LookupValueRepository           $lookupvalueRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->lookupRepository         = $lookupRepository;
        $this->lookupvalueRepository    = $lookupvalueRepository;
    } //Function ends


    /**
     * Get Collection of All Data
     *
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     *
     * @return mixed
     */
    public function index(string $orgHash, Collection $payload) {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');
            if (empty($user)) { 
                throw new AccessDeniedHttpException();
            } //End if

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Get request data
            $data = $payload->toArray();

            $response = $this->lookupRepository->getAllLookUpData($organization['id']);

            //Return the response data
            $objReturnValue = $response;            

        } catch(AccessDeniedHttpException $e) {
            log::error('LookupService:index:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('LookupService:index:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('LookupService:index:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Get Data by Lookup Key
     *
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function show(string $orgHash, Collection $payload, string $lookupKey) {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');
            if (empty($user)) { 
                throw new AccessDeniedHttpException();
            } //End if

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Get request data
            $response = $this->lookupRepository->getLookUpByKey($organization['id'], $lookupKey);

            //Return the response data
            $objReturnValue = $response;            

        } catch(AccessDeniedHttpException $e) {
            log::error('LookupService:index:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('LookupService:index:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('LookupService:index:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends