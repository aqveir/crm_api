<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class RemoteAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            //Check if the request exists
            if (empty($request)) { throw new BadRequestHttpException(); }

            //Validate the parameters
            if ($request->has('remote') && $request->has('secret')) {
                //Authenticate with remote credentials
                $attempt = Auth::validate([
                    'username' => $request['remote'], 
                    'password' => $request['secret'],
                    'is_remote_access_only' => true
                ], false);

                if ($attempt) {
                    //Get User
                    $user = config('aqveir-class.class_model.user.main')::where(['username' => $request['remote']])->first();

                    //Get User's organization
                    $organization = $user->organization;

                    if ((!empty($organization)) && ($organization['hash']==$request['key'])) {
                        //Do nothing
                    } else {
                        throw new AccessDeniedHttpException();
                    } //End if
                } else {
                    throw new AccessDeniedHttpException();
                } //End if
            } else { throw new BadRequestHttpException(); }

            return $next($request);
        } catch (Exception $e) {
            throw $e;
        } //Try-catch ends

    } //Function ends

} //Class ends
