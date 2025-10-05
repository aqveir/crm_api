<?php

namespace Modules\Core\Models\Common\Traits\Relationship;

/**
 * Class Currency Relationship
 */
trait CurrencyRelationship
{
	/**
	 * Country
	 */
	public function countries()
	{
		return $this->hasMany(
			config('aqveir-class.class_model.country'),
			'currency_code', 'iso_code'
		);
	} //Function ends

} //Trait ends
