<?php

namespace Modules\User\Services\User;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\User\Repositories\User\UserAvailabilityRepository;

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

    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var \Modules\User\Repositories\User\UserAvailabilityRepository
     */
    protected $useravailabilityRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\User\Repositories\User\UserAvailabilityRepository        $useravailabilityRepository
     */
    public function __construct(
        OrganizationRepository              $organizationRepository,
        UserAvailabilityRepository          $useravailabilityRepository
    ) {
        $this->organizationRepository       = $organizationRepository;
        $this->useravailabilityRepository   = $useravailabilityRepository;
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

            //Set Availability Status Key
            switch ($key) {
                case 'online':
                    $statusKey = 'user_status_online';
                    break;

                case 'away':
                    $statusKey = 'user_status_away';
                    break;
                
                default:
                    # code...
                    break;
            } //Switch ends

            //Assign to the return value
            $objReturnValue = $this->record($user['id'], $statusKey, $ipAddress);

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
     * @param \int $userId
     * @param \string $statusKey 
     * @param \string $ipAddress (optional)
     *
     * @return mixed
     */
    public function record(int $userId, string $statusKey, string $ipAddress=null)
    {
        $objReturnValue = null;

        try {
            //Record availability
            $response = $this->useravailabilityRepository->record($userId, $statusKey, $ipAddress);            

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