<?php

namespace Modules\Core\Models\Common\Traits\Relationship;

/**
 * Class Country Relationship
 */
trait CountryRelationship
{
	/**
	 * Currency
	 */
	public function currency()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.currency'),
			'iso_code', 'currency_code'
		)->where('is_active', true);
	} //Function ends


	/**
	 * TimeZones
	 */
	public function timezones()
	{
		return $this->hasMany(
			config('aqveir-class.class_model.timezone'),
			'country_id','id'
		)->where('is_active', true);
	} //Function ends

} //Trait ends
