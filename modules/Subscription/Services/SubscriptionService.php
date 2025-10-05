<?php

namespace Modules\Subscription\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Subscription\Repositories\SubscriptionRepository;
use Modules\Subscription\Repositories\StripeRepository;

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
 * Class SubscriptionService
 * @package Modules\Subscription\Services
 */
class SubscriptionService extends BaseService
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
     * @var  \Modules\Subscription\Repositories\StripeRepository
     */
    protected $stripeRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \Modules\Subscription\Repositories\SubscriptionRepository         $subscriptionRepository
     * @param \Modules\Subscription\Repositories\StripeRepository               $stripeRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        LookupValueRepository           $lookupRepository,
        SubscriptionRepository          $subscriptionRepository,
        StripeRepository                $stripeRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->lookupRepository         = $lookupRepository;
        $this->subscriptionRepository   = $subscriptionRepository;
        $this->stripeRepository         = $stripeRepository;
    } //Function ends


    /**
     * Get All Subscriptions
     * 
     * @param  \string  $orgHash
     * @param  \Illuminate\Support\Collection  $payload
     * @param  \bool $isAutoCreated (optional)
     * @param  \bool $isFiltered (optional)
     *
     * @return mixed
     */
    public function index(string $orgHash, Collection $payload, bool $isActive=null, bool $isFiltered=false)
    {
        $objReturnValue=null;
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Get subscription / invoices
            $response = $this->stripeRepository->getSubscriptions($orgHash, $organization['stripe_id']);            

            //Assign to the return value
            if (!empty($response)) {
                //Assign to the return value
                $objReturnValue = $response['data'];
            } //End if

        } catch(AccessDeniedHttpException $e) {
            log::error('SubscriptionService:index:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('SubscriptionService:index:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('SubscriptionService:index:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Get Subscriptions by unique identifier
     * 
     * @param  \string  $orgHash
     * @param  \Illuminate\Support\Collection  $payload
     * @param  \string  $uuid
     *
     * @return mixed
     */
    public function show(string $orgHash, Collection $payload, string $uuid)
    {
        $objReturnValue=null;
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Get subscription / invoices
            $response = $this->stripeRepository->getSubscriptionsByUuid($orgHash, $uuid);        

            //Assign to the return value
            if (!empty($response)) {
                //Assign to the return value
                $objReturnValue = $response;
            } //End if

        } catch(AccessDeniedHttpException $e) {
            log::error('SubscriptionService:index:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('SubscriptionService:index:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('SubscriptionService:index:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Create Subscription
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function create(string $orgHash, Collection $payload)
    {
        $objReturnValue=null; $data=[];
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Create new subscription
            if (in_array($payload['price'], config('subscription.settings.free_plans'))) {
                $paymentMethodId = null;
            } else {
                $paymentMethodId = $payload['paymentMethodId'];
            }
            Log::info(empty($paymentMethodId)?"free":"paid");

            $subscription = $organization->newSubscription('default', $payload['price'])->create();

            // //Build data
            // $data = $payload->only([
            //     'key', 'display_value', 'description', 'data_json',
            //     'order','is_displayed'
            // ])->toArray();
            // $data = array_merge($data, [
            //     'created_by' => $user['id'] 
            // ]);

            // //Create Subscription
            // $subscription = $this->subscriptionRepository->create($data);              

            //Assign to the return value
            $objReturnValue = $subscription;

        } catch(AccessDeniedHttpException $e) {
            log::error('SubscriptionService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('SubscriptionService:create:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('SubscriptionService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update Subscription
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $subscriptionId
     *
     * @return mixed
     */
    public function update(Collection $payload, int $subscriptionId)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Build data
            $data = $payload->only([
                'key', 'display_value', 'description', 'data_json',
                'order','is_displayed'
            ])->toArray();

            //Update Subscription
            $subscription = $this->subscriptionRepository->update($subscriptionId, 'id', $data, $user['id']);              

            //Assign to the return value
            $objReturnValue = $subscription;

        } catch(AccessDeniedHttpException $e) {
            log::error('SubscriptionService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('SubscriptionService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('SubscriptionService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Delete Subscription
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $subscriptionId
     *
     * @return mixed
     */
    public function delete(Collection $payload, int $subscriptionId)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get Subscription
            $subscription = $this->subscriptionRepository->getById($subscriptionId);

            //Delete Subscription
            $response = $this->subscriptionRepository->deleteById($subscriptionId, $user['id']);
            
            //Assign to the return value
            $objReturnValue = $response;

        } catch(AccessDeniedHttpException $e) {
            log::error('SubscriptionService:delete:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('SubscriptionService:delete:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('SubscriptionService:delete:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends