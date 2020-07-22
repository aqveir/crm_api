<?php

namespace Modules\Core\Models\Privilege\Traits\Relationship;

/**
 * Class Privilege Relationship
 */
trait PrivilegeRelationship
{
	public function roles()
	{
		return $this->belongsToMany(
			config('crmomni-class.class_model.role'), 
			config('crmomni-migration.table_name.role_privileges'),
			'privilege_id' , 'role_id' 
		);
	}

	public function users()
	{
		return $this->belongsToMany(
			config('crmomni-class.class_model.user.main'), 
			config('crmomni-migration.table_name.user.privileges'),
			'privilege_id' , 'user_id' 
		);
	}
}
