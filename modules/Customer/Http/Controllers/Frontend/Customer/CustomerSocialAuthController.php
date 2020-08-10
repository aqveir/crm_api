<?php

namespace Modules\Customer\Http\Controllers\Frontend\Customer;

use Config;
use Socialite;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Customer\Http\Requests\Frontend\Auth\CustomerSocialLoginRequest;
use Modules\Customer\Http\Requests\Frontend\Auth\CustomerSocialCallbackRequest;

use Modules\Customer\Services\Customer\CustomerService;
use Modules\Customer\Services\Customer\CustomerAuthService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller to Manage Login/Authentication
 */
class CustomerSocialAuthController extends ApiBaseController
{
    
    /**
     * Redirect the user to the Social Authentication page.
     *
     * @param \Modules\Api\Http\Requests\Frontend\Customer\CustomerSocialLoginRequest $request
     * 
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *      path="/customer/login/{social}",
     *      tags={"Customer"},
     *      operationId="api.frontend.customer.social.redirect",
     *      @OA\Parameter(
     *          ref="#/components/parameters/organization_key",
     *      ),
     *      @OA\Parameter(name="social", in="path", description="Social Auth Provider", required=true, @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function redirectToProvider(CustomerSocialLoginRequest $request, string $social)
    {
        return Socialite::driver($social)
            ->redirectUrl(config('services.'.$social.'.redirect').'?key='.$request['key'])
            ->stateless()
            ->redirect()
            ->getTargetUrl();
    } //Function ends

    
    /**
     * Obtain the customer information from Social Provider.
     *
     * @param \Modules\Api\Http\Requests\Frontend\Customer\CustomerSocialCallbackRequest $request
     * 
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     *      path="/customer/login/{social}/callback",
     *      tags={"Customer"},
     *      operationId="api.frontend.customer.social.callback",
     *      @OA\Parameter(
     *          ref="#/components/parameters/organization_key",
     *      ),
     *      @OA\Parameter(name="social", in="path", description="Social Auth Provider", required=true, @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function handleProviderCallback(CustomerSocialCallbackRequest $request, CustomerAuthService $customerAuthService, string $social)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Get the customer data
            $customer = Socialite::driver($social)
                ->redirectUrl(config('services.'.$social.'.redirect').'?key='.$request['key'])
                ->stateless()
                ->user();

            //Create Payload
            $payload=$this->getPayload($social, $customer);
            if ($payload) {
                $data = $customerAuthService->socialAuthenticate($orgHash, $social, collect($payload), $ipAddress);

                //Send http status out
                return $this->response->success(compact('data'));
            } else {
                throw new AccessDeniedHttpException();
            } //End if
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * 
     */
    private function getPayload(string $social, $customer) {
        $payload=null;

        if ($customer) {
            $customerName = $customer->getName();
            $firstNamePos = strpos($customerName, ' ');

            $payload=[];
            $payload['first_name']=substr($customerName, 0, $firstNamePos);
            $payload['last_name']=substr($customerName, ($firstNamePos+1), strlen($customerName));
            $payload['email']=$customer->getEmail();
            $payload['username']=$customer->getEmail();
        }
        return $payload;
    } //Function ends

} //Class ends
