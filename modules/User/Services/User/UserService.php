<?php

namespace Modules\User\Services\User;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\User\Repositories\User\UserRepository;

use Modules\Core\Services\BaseService;

use Modules\User\Events\UserCreatedEvent;

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
 * Class UserService
 * @package Modules\User\Services\User
 */
class UserService extends BaseService
{

    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var \Modules\User\Repositories\User\UserRepository
     */
    protected $userRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\User\Repositories\User\UserRepository    $userRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        UserRepository                  $userRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->userRepository           = $userRepository;
    } //Function ends


    /**
     * Create Default User
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $orgId
     * 
     * @return mixed
     */
    public function createDefault(Collection $payload, int $orgId) 
    {
        $objReturnValue=null;
        try {
            $data = $payload->only(['email', 'phone', 'first_name', 'last_name'])->toArray();
            $data = array_merge($data, [
                'username' => $data['email'],
                'password' => config('user.settings.new_organization.default_password'),
                'is_remote_access_only' => 0
            ]);

            //Create defult user
            $user = $this->create(collect($data), $orgId, true);
            if (empty($user)) {
                throw new BadRequestHttpException();
            } //End if

            //Store Additional Settings
            $user['is_active'] = true;
            $user['is_pool'] = true;
            $user['is_default'] = true;
            if ($user->save()) {
                throw new HttpException(500);
            } //End if

            //Assign to the return value
            $objReturnValue = $user;

        } catch(AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends    


    /**
     * Create User
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function create(string $orgHash, Collection $payload, bool $isAutoCreated=false)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            if ($user->hasRoles(config('crmomni.settings.default.role.key_super_admin'))) {
                //Get organization data
                $organization = $this->getOrganizationByHash($orgHash);
                $orgId = $organization['id'];
            } else {
                $orgId = $user['org_id'];
            } //End if

            //Build user data
            $data = $payload->only([
                'username', 'password', 'email', 'phone', 
                'first_name', 'last_name', 'is_remote_access_only'
            ])->toArray();

            // Duplicate check
            $isDuplicate=$this->userRepository->exists($data['username'], 'username');
            if (!$isDuplicate) {
                //Add Organisation data
                $data = array_merge($data, [ 'org_id' => $orgId, 'created_by' => $user['id'] ]);

                //Create User
                $user = $this->userRepository->create($data);

                //Raise event: User Added
                event(new UserCreatedEvent($user, $isAutoCreated));
            } else {
                throw new BadRequestHttpException();
            } //End if

            //Assign to the return value
            $objReturnValue = $user;

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
