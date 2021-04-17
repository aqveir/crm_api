<?php

namespace Modules\Preference\Models\Preference\Traits\Relationship;

use Modules\Preference\Models\Preference\Preference;
use Modules\Preference\Models\Preference\PreferenceData;

/**
 * Class Preference Data Values Relationship
 */
trait PreferenceDataValueRelationship
{  
    /**
	 * Preference Lookup Values for JSON Data Type
	 */
	public function data()
	{
		return $this->belongsTo(
			PreferenceData::class,
			'data_id', 'id'
		);
	}

} //Trait ends
