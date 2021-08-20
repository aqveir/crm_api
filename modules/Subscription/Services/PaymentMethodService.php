<?php

namespace Modules\Subscription\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Subscription\Repositories\SubscriptionRepository;

use Modules\Core\Services\BaseService;

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
 * Class PaymentMethodService
 * @package Modules\Subscription\Services
 */
class PaymentMethodService extends BaseService
{

    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var Modules\Core\Repositories\Lookup\LookupValueRepository
     */
    protected $lookupRepository;


    /**
     * @var \Modules\Subscription\Repositories\SubscriptionRepository
     */
    protected $subscriptionRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \Modules\Subscription\Repositories\SubscriptionRepository         $subscriptionRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        LookupValueRepository           $lookupRepository,
        SubscriptionRepository          $subscriptionRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->lookupRepository         = $lookupRepository;
        $this->subscriptionRepository   = $subscriptionRepository;
    } //Function ends


    /**
     * Get All Organization Payment Methods
     * 
     * @param  \string  $orgHash
     * @param  \Illuminate\Support\Collection  $payload
     *
     * @return mixed
     */
    public function index(string $orgHash, Collection $payload)
    {
        $objReturnValue=null; $records=null;
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Check for existing payment methods exists
            if ($organization->hasPaymentMethod()) {
                $paymentMethods = $organization->paymentMethods();
                if (!empty($paymentMethods)) {
                    //Convert to array
                    $records = $paymentMethods->toArray();

                    $defaultPaymentMethod = null;
                    if ($organization->hasDefaultPaymentMethod()) {
                        $defaultPaymentMethod = $organization->defaultPaymentMethod();
                    } //End if

                    //Set default flag
                    foreach ($records as &$record) {
                        $record['is_default'] = (!empty($defaultPaymentMethod) && ($defaultPaymentMethod->id==$record['id']))?true:false;
                    } //Loop ends                    
                } //End if
            } //End if

            //Assign to the return value
            $objReturnValue = $records;

        } catch(AccessDeniedHttpException $e) {
            log::error('PaymentMethodService:index:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('PaymentMethodService:index:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('PaymentMethodService:index:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Get Setup Intent for the Organization
     * 
     * @param  \string  $orgHash
     * @param  \Illuminate\Support\Collection  $payload
     *
     * @return mixed
     */
    public function setupIntent(string $orgHash, Collection $payload)
    {
        $objReturnValue=null; $paymentMethods=null;
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Assign to the return value
            $objReturnValue = $organization->createSetupIntent();

        } catch(AccessDeniedHttpException $e) {
            log::error('PaymentMethodService:intent:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('PaymentMethodService:intent:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('PaymentMethodService:intent:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Create Payment Method
     * 
     * @param  \string  $orgHash
     * @param  \Illuminate\Support\Collection  $payload
     * @param  \bool  $isForced (optional)
     *
     * @return mixed
     */
    public function create(string $orgHash, Collection $payload, bool $isForced=false)
    {
        $objReturnValue=null; $paymentMethod=null;
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Create and Make default payment method, if none exists
            if (!($organization->hasDefaultPaymentMethod()) || $isForced) {
                $paymentMethod = $organization->updateDefaultPaymentMethod($payload['payment_method']); //Create and Make Default Payment Method
            } else {
                $paymentMethod = $organization->addPaymentMethod($payload['payment_method']); //Create Payment Method
            } //End if

            //Assign to the return value
            $objReturnValue = $paymentMethod;

        } catch(AccessDeniedHttpException $e) {
            log::error('PaymentMethodService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('PaymentMethodService:create:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('PaymentMethodService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update Payment Method
     * 
     * @param  \string  $orgHash
     * @param  \Illuminate\Support\Collection $payload
     * @param  \string  $paymentMethodId
     *
     * @return mixed
     */
    public function update(string $orgHash, Collection $payload, string $paymentMethodId)
    {
        $objReturnValue=null;$paymentMethod=null;
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Create and Make default payment method, if none exists
            if ($payload->has('is_default') && $payload['is_default']) {
                $paymentMethod = $organization->updateDefaultPaymentMethod($paymentMethodId); //Make Default Payment Method
            } //End if

            //Assign to the return value
            $objReturnValue = $paymentMethod;

        } catch(AccessDeniedHttpException $e) {
            log::error('PaymentMethodService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('PaymentMethodService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('PaymentMethodService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Delete Payment Method
     * 
     * @param  \string  $orgHash
     * @param  \Illuminate\Support\Collection  $payload
     * @param  \string  $paymentMethodId
     *
     * @return mixed
     */
    public function delete(string $orgHash, Collection $payload, string $paymentMethodId)
    {
        $objReturnValue=null;
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Get Payment Method
            if (!($organization->hasPaymentMethod())) {
                throw new BadRequestHttpException();
            } //End if

            $paymentMethod = $organization->findPaymentMethod($paymentMethodId);
            if (empty($paymentMethod)) {
                throw new BadRequestHttpException('Selected Payment Method Missing');
            } //End if

            //Delete Payment Method
            $paymentMethod->delete();
            
            //Assign to the return value
            $objReturnValue = $paymentMethod;

        } catch(AccessDeniedHttpException $e) {
            log::error('PaymentMethodService:delete:AccessDeniedHttpException:' . $e->getMessage());
            throw $e;
        } catch(BadRequestHttpException $e) {
            log::error('PaymentMethodService:delete:BadRequestHttpException:' . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            log::error('PaymentMethodService:delete:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends