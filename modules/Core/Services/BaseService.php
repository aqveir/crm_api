<?php

namespace Modules\Core\Services;

use Config;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class BaseService
 * @package Modules\Core\Services
 */
abstract class BaseService
{
    /**
     * @var Modules\Core\Repositories\Lookup\LookupValueRepository
     */
    protected $lookuprepository;


    /**
     * @var  \Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;

    /**
     *
     * @return \Laravel\Auth\Guard
     */
    public function guard(string $guard='backend')
    {
        return Auth::guard($guard);
    } //Function ends


    /**
     * Authenticate method
     */
    public function attempt($credentials)
    {
        $objReturnValue = null;

        try {
            $guard = $this->guard();

            if ($guard instanceof \Illuminate\Auth\SessionGuard) {
                $objReturnValue = $guard->attempt($credentials);
            } elseif ($guard instanceof \Illuminate\Auth\RequestGuard) {
                dd($guard->validate($credentials));
            } elseif ($guard instanceof \Illuminate\Auth\TokenGuard) {
                
            } else {
                $objReturnValue = null;
            } //End if
        } catch(Exception $e) {
            log::error($e);
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends
    

    /**
     *
     * @return \Modules\Core\Models\User\User
     */
    public function getCurrentUser(string $guard='backend') 
    {
        $objReturnValue=null;

        try {
            //Get user data
            $user = $this->guard($guard)->user();

            $objReturnValue = $user;
        } catch(Exception $e) {
            throw new UnauthorizedHttpException(401);
        } //try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * @param \Illuminate\Http\Request   $request
     * @param \Modules\Core\Models\User\User|null $user
     *
     * @return \Laravel\JWT\PersonalAccessTokenResult
     */
    public function generateAccessToken(Request $request, User $user = null)
    {
        return $request->user() ? $request->user()->createToken('Personal Access Token') : $user->createToken('Personal Access Token');
    } //Function ends


    /**
     * Get Organization By Hash
     */
    public function getOrganizationByHash(string $hash)
    {
        $objReturnValue=null;

        try {
            //Get organization data
            $organization = $this->organizationRepository->getOrganizationByHash($hash);

            $objReturnValue = $organization;
        } catch(Exception $e) {
            throw new Exception($e);
        } //try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Get Lookup Value By Key
     */
    public function getLookupValueByKey(int $orgId, string $key)
    {
        $objReturnValue=null;

        try {
            //Get organization data
            $lookupvalue = $this->lookuprepository->getLookUpByKey($orgId, $key);
            if (empty($lookupvalue)) {
                throw new ModelNotFoundException();
            } //End if

            $objReturnValue = $lookupvalue;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException($e);
        } catch(Exception $e) {
            throw new Exception($e);
        } //try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends