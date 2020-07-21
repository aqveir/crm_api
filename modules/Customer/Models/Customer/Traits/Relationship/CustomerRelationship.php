<?php

namespace Modules\Customer\Models\Customer\Traits\Relationship;

use Exception;
use Modules\Customer\Models\Customer\Company;
use Modules\Customer\Models\Customer\CustomerAddress;
use Modules\Customer\Models\Customer\CustomerDetail;
use Modules\Wallet\Models\Wallet\Wallet;
use Modules\OMS\Models\Order\Order;

/**
 * Class Customer Relationship
 */
trait CustomerRelationship
{
	/**
	 * Customer Addresses
	 */
	public function addresses()
	{
		return $this->hasMany(
			CustomerAddress::class, 
			'customer_id', 'id'
		);
	}


	/**
	 * Customer Details
	 */
	public function details()
	{
		return $this->hasMany(
			CustomerDetail::class, 
			'customer_id', 'id'
		);
	}


	/**
	 * Customers Wallets
	 */
	public function wallets()
	{
		return $this->belongsToMany(
			CustomerDetail::class,
			config('omnicrm-migration.table_name.wallet.customers'),
			'customer_id', 
			'wallet_id'
		)->wherePivot('is_active', 1);
	}


	/**
	 * Top10 Orders for the Customer
	 */
	public function orders()
	{
		try {
			return $this->hasMany(
				Order::class,
				'customer_id', 'id'
			)
			->orderBy('created_at', 'desc')
			->take(10);			
		} catch(Exception $e) {
			return [];
		} //Try-catch ends
	} //Function ends


	/**
	 * Top5 Notes for the Customer
	 */
	public function notes()
	{
		return $this->hasMany(
			config('omnicrm-class.class_model.note'),
			'reference_id', 'id'
		)
		->whereHas('type', function($inner){$inner->where('key', 'entity_type_customer');})
		->orderBy('created_at', 'desc')
		->take(5);
	}


	/**
	 * Top5 Notes for the Customer
	 */
	public function documents()
	{
		return $this->hasMany(
			config('omnicrm-class.class_model.document'),
			'reference_id', 'id'
		)
		->whereHas('type', function($inner){$inner->where('key', 'entity_type_customer');})
		->orderBy('created_at', 'desc');
	}


	/**
	 * Organization Onboarding the Customer
	 */
	public function organization()
	{
		return $this->belongsTo(
			config('omnicrm-class.class_model.organization'),
			'customer_id', 'id'
		);
	}


	/**
	 * Customer' Occupation
	 */
	public function occupation()
	{
		return $this->hasOne(
			config('omnicrm-class.class_model.lookup_value'),
			'id', 'occupation_id'
		);
	}


	/**
	 * Gender of the Customer
	 */
	public function gender()
	{
		return $this->hasOne(
			config('omnicrm-class.class_model.lookup_value'),
			'id', 'gender_id'
		);
	}


	/**
	 * Customer Group
	 */
	public function group()
	{
		return $this->hasOne(
			config('omnicrm-class.class_model.lookup_value'),
			'id', 'group_id'
		);
	}


	/**
	 * Type of the Customer
	 */
	public function type()
	{
		return $this->hasOne(
			config('omnicrm-class.class_model.lookup_value'),
			'id', 'type_id'
		);
	}

} //Trait ends
