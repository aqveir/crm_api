<?php

namespace Modules\Contact\Models\Contact\Traits\Relationship;

use Exception;
use Modules\Contact\Models\Contact\Company;
use Modules\Contact\Models\Contact\ContactAddress;
use Modules\Contact\Models\Contact\ContactDetail;
use Modules\Wallet\Models\Wallet\Wallet;
use Modules\OMS\Models\Order\Order;

/**
 * Class Contact Relationship
 */
trait ContactRelationship
{
	/**
	 * Contact Addresses
	 */
	public function addresses()
	{
		return $this->hasMany(
			ContactAddress::class, 
			'contact_id', 'id'
		);
	}


	/**
	 * Contact Details
	 */
	public function details()
	{
		return $this->hasMany(
			ContactDetail::class, 
			'contact_id', 'id'
		);
	}


	/**
	 * Contacts Wallets
	 */
	public function wallets()
	{
		return $this->belongsToMany(
			ContactDetail::class,
			config('crmomni-migration.table_name.wallet.contacts'),
			'contact_id', 
			'wallet_id'
		)->wherePivot('is_active', 1);
	}


	/**
	 * Top10 Orders for the Contact
	 */
	public function orders()
	{
		try {
			return $this->hasMany(
				Order::class,
				'contact_id', 'id'
			)
			->orderBy('created_at', 'desc')
			->take(10);			
		} catch(Exception $e) {
			return [];
		} //Try-catch ends
	} //Function ends


	/**
	 * Top5 Notes for the Contact
	 */
	public function notes()
	{
		return $this->hasMany(
			config('crmomni-class.class_model.note'),
			'reference_id', 'id'
		)
		->whereHas('type', function($inner){$inner->where('key', 'entity_type_contact');})
		->orderBy('created_at', 'desc')
		->take(5);
	}


	/**
	 * Top5 Notes for the Contact
	 */
	public function documents()
	{
		return $this->hasMany(
			config('crmomni-class.class_model.document'),
			'reference_id', 'id'
		)
		->whereHas('type', function($inner){$inner->where('key', 'entity_type_contact');})
		->orderBy('created_at', 'desc');
	}


	/**
	 * Organization Onboarding the Contact
	 */
	public function organization()
	{
		return $this->belongsTo(
			config('crmomni-class.class_model.organization'),
			'contact_id', 'id'
		);
	}


	/**
	 * Contact Occupation
	 */
	public function occupation()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'),
			'id', 'occupation_id'
		);
	}


	/**
	 * Gender of the Contact
	 */
	public function gender()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'),
			'id', 'gender_id'
		);
	}


	/**
	 * Contact Group
	 */
	public function group()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'),
			'id', 'group_id'
		);
	}


	/**
	 * Type of the Contact
	 */
	public function type()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'),
			'id', 'type_id'
		);
	}

} //Trait ends
