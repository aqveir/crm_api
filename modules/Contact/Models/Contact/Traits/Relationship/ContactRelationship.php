<?php

namespace Modules\Contact\Models\Contact\Traits\Relationship;

use Exception;
use Modules\Contact\Models\Contact\Company;
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
			'Modules\Contact\Models\Contact\ContactAddress', 
			'contact_id', 'id'
		);
	} //Function ends


	/**
	 * Contact Details
	 */
	public function details()
	{
		return $this->hasMany(
			'Modules\Contact\Models\Contact\ContactDetail', 
			'contact_id', 'id'
		);
	} //Function ends


	/**
	 * Service Requests of the contact
	 */
	public function service_requests()
	{
		return $this->hasMany(
			config('aqveir-class.class_model.service_request.main'),
			'contact_id', 'id'
		)
		->orderBy('created_at', 'desc');
	} //Function ends


	/**
	 * Service Requests of the contact
	 */
	public function active_service_requests()
	{
		return $this->hasMany(
			config('aqveir-class.class_model.service_request.main'),
			'contact_id', 'id'
		)
		->with(['status'])
		->whereHas('status', function($inner_query) {
			$inner_query->whereIn('key', ['service_request_status_new', 'service_request_status_active']);
		})
		->orderBy('created_at', 'desc');
	} //Function ends


	/**
	 * Contacts Wallets
	 */
	public function wallets()
	{
		return $this->belongsToMany(
			ContactDetail::class,
			config('aqveir-migration.table_name.wallet.contacts'),
			'contact_id', 
			'wallet_id'
		)->wherePivot('is_active', 1);
	}//Function ends


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
		if (class_exists(config('aqveir-class.class_model.note'))) {
			return $this->hasMany(
				config('aqveir-class.class_model.note'),
				'reference_id', 'id'
			)
			->with(['type', 'owner'])
			->whereHas('type', function($inner_query){$inner_query->where('key', 'entity_type_contact');})
			->orderBy('created_at', 'desc');
		} else {
			return [];
		} //End if
	} //Function ends


	/**
	 * Documents for the Contact
	 */
	public function documents()
	{
		return $this->hasMany(
			config('aqveir-class.class_model.document'),
			'reference_id', 'id'
		)
		->with(['type', 'owner'])
		->whereHas('type', function($inner_query){$inner_query->where('key', '=', 'entity_type_contact');})
        ->where('is_active', 1)
		->orderBy('created_at', 'desc');
	} //Function ends


	/**
	 * Organization Onboarding the Contact
	 */
	public function organization()
	{
		return $this->belongsTo(
			config('aqveir-class.class_model.organization'),
			'org_id', 'id'
		);
	} //Function ends


	/**
	 * Contact Occupation
	 */
	public function occupation()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'occupation_id'
		);
	} //Function ends


	/**
	 * Gender of the Contact
	 */
	public function gender()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'gender_id'
		);
	} //Function ends


	/**
	 * Contact Group
	 */
	public function group()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'group_id'
		);
	} //Function ends


	/**
	 * Type of the Contact
	 */
	public function type()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'type_id'
		);
	} //Function ends


	/**
	 * Status of the Contact
	 */
	public function status()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'status_id'
		);
	} //Function ends

} //Trait ends
