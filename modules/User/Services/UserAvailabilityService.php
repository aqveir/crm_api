<?php

namespace Modules\User\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\User\Repositories\User\UserAvailabilityRepository;
use Modules\User\Repositories\User\UserRepository;
use Modules\User\Transformers\Responses\UserAvailabilityResource;
use Modules\User\Traits\UserAvailabilityAction;

use Modules\Core\Services\BaseService;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Modules\Core\Exceptions\ExistingDataException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class UserAvailabilityService
 * @package Modules\User\Services\User
 */
class UserAvailabilityService extends BaseService
{
    use UserAvailabilityAction;

    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var \Modules\User\Repositories\User\UserAvailabilityRepository
     */
    protected $useravailabilityRepository;


    /**
     * @var \Modules\User\Repositories\User\UserRepository
     */
    protected $userRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\User\Repositories\User\UserAvailabilityRepository        $useravailabilityRepository
     * @param \Modules\User\Repositories\User\UserRepository                    $userRepository
     */
    public function __construct(
        OrganizationRepository              $organizationRepository,
        UserAvailabilityRepository          $useravailabilityRepository,
        UserRepository                      $userRepository
    ) {
        $this->organizationRepository       = $organizationRepository;
        $this->useravailabilityRepository   = $useravailabilityRepository;
        $this->userRepository               = $userRepository;
    } //Function ends


    /**
     * Fetch User Availability
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \string $hash
     *
     * @return mixed
     */
    public function fetch(string $orgHash, Collection $payload, string $hash=null) {
        $objReturnValue = null;

        try {
            $user = null;
            if (empty($hash)) {
                $user = $this->getCurrentUser('backend'); //Authenticated User
            } else {
                //Get organization data
                $organization = $this->getOrganizationByHash($orgHash);
                if (empty($organization)) { throw new BadRequestHttpException(); } //End if

                $user = $this->userRepository->getDataByHash($organization['id'], $hash);
            } //End if

            //Fetch record
            $response = $this->useravailabilityRepository
                ->where('org_id', $user->organization['id'])
                ->where('user_id', $user['id'])
                ->first();

            $response->load(['user', 'status']);

            $objReturnValue = new UserAvailabilityResource($response);
        } catch(NotFoundHttpException $e) {
            log::error('UserAvailabilityService:fetch:NotFoundHttpException:' . $e->getMessage());
            throw new NotFoundHttpException();
        } catch(BadRequestHttpException $e) {
            log::error('UserAvailabilityService:fetch:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserAvailabilityService:fetch:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update User Availability
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \string $key 
     * @param \string $ipAddress (optional)
     *
     * @return mixed
     */
    public function update(Collection $payload, string $key, string $ipAddress=null) {
        $objReturnValue = null; $statusKey = null;

        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');
            $organization = $user->organization;

            //Assign to the return value
            $objReturnValue = $this->record($organization['id'], $user['id'], $key, $ipAddress);

        } catch(ExistingDataException $e) {
            throw new ExistingDataException();
        } catch(BadRequestHttpException $e) {
            log::error('UserService:register:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:register:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;

    } //Function ends


    /**
     * Record User Availability
     * 
     * @param \int $orgId
     * @param \int $userId
     * @param \string $statusKey 
     * @param \string $ipAddress (optional)
     *
     * @return mixed
     */
    public function record(int $orgId, int $userId, string $statusKey, string $ipAddress=null)
    {
        $objReturnValue = null;

        try {
            //Record availability
            $response = $this->useravailabilityRepository->record($orgId, $userId, $statusKey, $ipAddress);            

            //Assign to the return value
            $objReturnValue = $response;

        } catch(ExistingDataException $e) {
            throw new ExistingDataException();
        } catch(AccessDeniedHttpException $e) {
            log::error('UserAvailabilityService:record:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('UserAvailabilityService:record:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserAvailabilityService:record:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends