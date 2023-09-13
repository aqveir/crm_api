<?php

namespace Modules\Contact\Http\Controllers\Frontend\Contact;

use Config;
use Socialite;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Contact\Http\Requests\Frontend\Auth\ContactSocialLoginRequest;
use Modules\Contact\Http\Requests\Frontend\Auth\ContactSocialCallbackRequest;

use Modules\Contact\Services\Contact\ContactService;
use Modules\Contact\Services\Contact\ContactAuthService;

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
class ContactSocialAuthController extends ApiBaseController
{
    
    /**
     * Redirect the user to the Social Authentication page.
     *
     * @param \Modules\Api\Http\Requests\Frontend\Contact\ContactSocialLoginRequest $request
     * 
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *      path="/contact/login/{social}",
     *      tags={"Contact"},
     *      operationId="api.frontend.contact.social.redirect",
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
    public function redirectToProvider(ContactSocialLoginRequest $request, string $domain, string $social)
    {
        return Socialite::driver($social)
            ->redirectUrl(config('services.'.$social.'.redirect').'?key='.$request['key'])
            ->stateless()
            ->redirect()
            ->getTargetUrl();
    } //Function ends

    
    /**
     * Obtain the contact information from Social Provider.
     *
     * @param \Modules\Api\Http\Requests\Frontend\Contact\ContactSocialCallbackRequest $request
     * 
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     *      path="/contact/login/{social}/callback",
     *      tags={"Contact"},
     *      operationId="api.frontend.contact.social.callback",
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
    public function handleProviderCallback(ContactSocialCallbackRequest $request, ContactAuthService $customerAuthService, string $social)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Get the contact data
            $contact = Socialite::driver($social)
                ->redirectUrl(config('services.'.$social.'.redirect').'?key='.$request['key'])
                ->stateless()
                ->user();

            //Create Payload
            $payload=$this->getPayload($social, $contact);
            if ($payload) {
                $data = $customerAuthService->socialAuthenticate($orgHash, $social, collect($payload), $ipAddress);

                //Send response data
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
    private function getPayload(string $social, $contact) {
        $payload=null;

        if ($contact) {
            $customerName = $contact->getName();
            $firstNamePos = strpos($customerName, ' ');

            $payload=[];
            $payload['first_name']=substr($customerName, 0, $firstNamePos);
            $payload['last_name']=substr($customerName, ($firstNamePos+1), strlen($customerName));
            $payload['email']=$contact->getEmail();
            $payload['username']=$contact->getEmail();
        }
        return $payload;
    } //Function ends

} //Class ends
