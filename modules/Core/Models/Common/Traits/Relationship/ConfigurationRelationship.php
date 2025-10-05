<?php

namespace Modules\Core\Models\Common\Traits\Relationship;

/**
 * Class Configuration Relationship
 */
trait ConfigurationRelationship
{
	/**
	 * Type
	 */
	public function type()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'type_id'
		);
	} //Function ends

} //Trait ends