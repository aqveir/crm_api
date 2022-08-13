<?php

namespace Modules\Core\Models\Common\Traits\Relationship;

/**
 * Class Country Data Relationship
 */
trait MapCountryDataRelationship
{
	/**
	 * Country
	 */
	public function country()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.country'),
			'id', 'country_id'
		)->where('is_active', true);
    } //Function ends
    

	/**
	 * Currency
	 */
	public function currency()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.currency'),
			'id', 'currency_id'
		)->where('is_active', true);
	} //Function ends


	/**
	 * TimeZone
	 */
	public function timezone()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.timezone'),
			'id', 'timezone_id'
		)->where('is_active', true);
	} //Function ends

} //Trait ends
