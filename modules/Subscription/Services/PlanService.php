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
 * Class PlanService
 * @package Modules\Subscription\Services
 */
class PlanService extends BaseService
{

    /**
     * @var  \Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var  \Modules\Core\Repositories\Lookup\LookupValueRepository
     */
    protected $lookupRepository;


    /**
     * @var  \Modules\Subscription\Repositories\SubscriptionRepository
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
     * Get All Prices/Plans
     * 
     * @param  \Illuminate\Support\Collection  $payload
     *
     * @return mixed
     */
    public function index(Collection $payload)
    {
        $objReturnValue=null;
        try {
            //Get Plans/Products/Items
            $response = $this->stripeRepository->getPrices();

            if (!empty($response)) {
                //Assign to the return value
                $objReturnValue = $response['data'];
            } //End if
        } catch(AccessDeniedHttpException $e) {
            log::error('PlanService:index:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('PlanService:index:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('PlanService:index:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Create Price/Plan for the existing Product
     * 
     * @param  \Illuminate\Support\Collection  $payload
     *
     * @return mixed
     */
    public function create(Collection $payload)
    {
        $objReturnValue=null;
        try {
            //Create Pricing/Plan
            $response = $this->stripeRepository->getStripeClient()->prices->create($payload);

            if (!empty($response)) {
                //Assign to the return value
                $objReturnValue = $response['data'];
            } //End if
        } catch(AccessDeniedHttpException $e) {
            log::error('PlanService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('PlanService:create:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('PlanService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update All Price/Plan form the configuration
     * 
     * @param  \Illuminate\Support\Collection  $payload
     *
     * @return mixed
     */
    public function updateAll(Collection $payload)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Check user privileges
            if (!$user->hasRoles(['super_admin'])) {
                throw new AccessDeniedHttpException('super admin only');
            } //End if

            //Pricings data
            $pricings = config('subscription.settings.pricings');
            if ((!empty($pricings)) && is_array($pricings)) {
                foreach ($pricings as $priceUuid => $pricing) {
                    //Update Pricing/Plan
                    $response = $this->update($priceUuid, collect($pricing));
                } //Loop ends
            } //End if

            //Reload the pricing and force cache refresh
            $response = $this->stripeRepository->getPrices(true);

            //Assign to the return value
            $objReturnValue = true;
        } catch(AccessDeniedHttpException $e) {
            log::error('PlanService:updateAll:AccessDeniedHttpException:' . $e->getMessage());
            throw $e;
        } catch(BadRequestHttpException $e) {
            log::error('PlanService:updateAll:BadRequestHttpException:' . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            log::error('PlanService:updateAll:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update Price/Plan for the existing Price uuid
     * 
     * @param  \string  $priceUuid
     * @param  \Illuminate\Support\Collection  $payload
     *
     * @return mixed
     */
    public function update(string $priceUuid, Collection $payload)
    {
        $objReturnValue=null;
        try {
            //Update Pricing/Plan
            $response = $this->stripeRepository->getStripeClient()->prices->update($priceUuid, $payload->only([
                'nickname', 'metadata', 'active'
            ]));

            if (!empty($response)) {
                //Assign to the return value
                $objReturnValue = $response['data'];
            } //End if
        } catch(AccessDeniedHttpException $e) {
            log::error('PlanService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('PlanService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('PlanService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends