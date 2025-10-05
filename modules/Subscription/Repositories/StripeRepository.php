<?php

namespace Modules\Subscription\Repositories;

use Stripe\StripeClient;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Class StripeRepository
 * 
 * @package Module\Subscription\Repositories
 */
class StripeRepository //implements SubscriptionContract
{
    private $stripeClient;


    /**
     * Repository constructor.
     *
     */
    public function __construct()
    {
        $this->stripeClient = new StripeClient(config('subscription.settings.stripe_secret_key'));
    }


	/**
	 * Get Stripe Client
	 */
	public function getStripeClient()
	{
		$objReturnValue=null;
		
		try {
            $objReturnValue = $this->stripeClient;
		} catch(Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-catch ends
		
		return $objReturnValue;
	} //Function ends


	/**
	 * Get All Stripe Products
	 */
	public function getProducts()
	{
		$objReturnValue=null;
		
		try {
            //Get cache configuration
            $keyCache = config('subscription.settings.cache.stripe_products.key');
            $durationCache = config('subscription.settings.cache.stripe_products.duration_in_sec');

            if (Cache::has($keyCache)) {
                $objReturnValue = Cache::get($keyCache);
            } else {
                $objReturnValue = Cache::remember($keyCache, $durationCache/60, function() {
                    return $this->stripeClient->products->all();
                });
            } //End if-else
		} catch(Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-catch ends
		
		return $objReturnValue;
	} //Function ends


	/**
	 * Get All Stripe Prices
	 */
	public function getPrices(bool $isForced=false)
	{
		$objReturnValue=null;
		
		try {
            //Get cache configuration
            $keyCache = config('subscription.settings.cache.stripe_prices.key');
            $durationCache = config('subscription.settings.cache.stripe_prices.duration_in_sec');

            //Force the cache to be cleared
            if ($isForced) {
                Cache::forget($keyCache);
            } //End if

            if (Cache::has($keyCache)) {
                $objReturnValue = Cache::get($keyCache);
            } else {
                $objReturnValue = Cache::remember($keyCache, $durationCache/60, function() {
                    return $this->stripeClient->prices->all();
                });
            } //End if-else
		} catch(Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-catch ends
		
		return $objReturnValue;
	} //Function ends


	/**
	 * Get All Stripe Subscriptions by Organization UUID
	 */
	public function getSubscriptions(string $orgHash, string $organizationUuid, bool $isForced=false)
	{
		$objReturnValue=null;
		
		try {
            //Get cache configuration
            $keyCache = config('subscription.settings.cache.stripe_subscriptions.key') . $organizationUuid;
            $durationCache = config('subscription.settings.cache.stripe_subscriptions.duration_in_sec');

            //Force the cache to be cleared
            if ($isForced) {
                Cache::forget($keyCache);
            } //End if

            if (Cache::has($keyCache)) {
                $objReturnValue = Cache::get($keyCache);
            } else {
                $objReturnValue = Cache::remember($keyCache, $durationCache/60, function() use ($organizationUuid) {
                    return $this->stripeClient->subscriptions->all(['customer' => $organizationUuid, 'status' => 'all']);
                });
            } //End if-else
		} catch(Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-catch ends
		
		return $objReturnValue;
	} //Function ends


	/**
	 * Get Stripe Subscription by UUID
	 */
	public function getSubscriptionsByUuid(string $orgHash, string $subscriptionUuid)
	{
		$objReturnValue=null;
		
		try {
			return $this->stripeClient->subscriptions->retrieve($subscriptionUuid, []);
		} catch(Exception $e) {
			$objReturnValue=null;
		} //Try-catch ends
		
		return $objReturnValue;
	} //Function ends


	/**
	 * Create Stripe Subscriptions by Organization UUID and Price UUID
	 */
	public function createSubscriptions(string $orgHash, string $organizationUuid, string $priceUuid)
	{
		$objReturnValue=null;
		
		try {
            return $this->stripeClient->subscriptions->create(['customer' => $organizationUuid, 'items' => [
				'price' => $priceUuid
			]]);
		} catch(Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-catch ends
		
		return $objReturnValue;
	} //Function ends
	
} //Class ends