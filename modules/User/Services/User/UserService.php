<?php

namespace Modules\User\Services\User;

use Config;
use Carbon\Carbon;

use Modules\User\Models\User\User;

use Modules\User\Repositories\User\UserRepository;

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
 * Class UserService
 * @package Modules\Core\Services\User
 */
class UserService extends BaseService
{
    /**
     * @var Modules\Core\Repositories\User\UserRepository
     */
    protected $userrepository;


    /**
     * @var \Modules\Core\Models\User\User
     */
    protected $user;


    /**
     * AuthService constructor.
     *
     * @param \Modules\Core\Models\User\User    $user
     */
    public function __construct(
        UserRepository $userrepository,
        User $user
    ) {
        $this->userrepository        = $userrepository;
        $this->user                  = $user;
    } //Function ends


    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function createUser(Request $request, int $orgId, bool $isRemoteAccessOnly=false)
    {
        $objReturnValue=null;
        try {
            $payload = $request->only('username', 'password', 'email', 'first_name', 'last_name');

            // Duplicate check
            $isDuplicate=$this->userrepository->exists($payload['username'], 'username');
            if(!$isDuplicate) {

                //Generate the hash code for the user
                $hash=$this->generateRandomHash('u');

                //Generate the data payload to create user
                $payload = array_merge(
                    $payload,
                    [
                        'hash' => $hash, 'org_id' => $orgId,
                        'is_active' => 1, 'is_verified' => 0, 
                        'is_remote_access_only' => $isRemoteAccessOnly
                    ]
                );

                //Create User
                $objReturnValue = $this->userrepository->create($payload);
            } else {
                throw new BadRequestHttpException();
            } //End if
        } catch(Exception $e) {

        }
        return $objReturnValue;
    } //Function ends

} //Class ends
