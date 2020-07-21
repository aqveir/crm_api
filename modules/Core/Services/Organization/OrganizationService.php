<?php

namespace Modules\Core\Services\Organization;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;

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
 * Class OrganizationService
 * 
 * @package Modules\Core\Services\Organization
 */
class OrganizationService extends BaseService
{
    /**
     * @var \Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * CountryService constructor.
     *
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     */
    public function __construct(
        OrganizationRepository               $organizationRepository
    ) {
        $this->organizationRepository        = $organizationRepository;
    } //Function ends


    /**
     * Get all organization data
     */
    public function getAll(Request $request, bool $isActive=null)
    {
        $objReturnValue=null;
        try {
            $objReturnValue = $this->organizationRepository->getAllOrganizationsData();
                
        } catch(AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            Log::error($e);
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Get all organization data
     */
    public function getData(Request $request, string $hash, bool $isActive=null)
    {
        $objReturnValue=null;
        try {
            $objReturnValue = $this->organizationRepository->getOrganizationByHash($hash, $isActive);
                
        } catch(AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            Log::error($e);
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends