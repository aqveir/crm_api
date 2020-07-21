<?php

namespace Modules\Core\Models\Role\Traits\Relationship;

/**
 * Class Role Relationship
 */
trait RoleRelationship
{
	public function users()
	{
		return $this->belongsToMany(
			config('omnicrm-class.class_model.user'),
			config('omnicrm-migration.table_name.user_roles'),
			'role_id', 'user_id'
		);
	}

	public function privileges()
	{
		return $this->belongsToMany(
			config('omnicrm-class.class_model.privilege'),
			config('omnicrm-migration.table_name.role_privileges'),
			'role_id', 'privilege_id'
		);
	}

	public function active_privileges()
	{
		return $this->belongsToMany(
			config('omnicrm-class.class_model.privilege'),
			config('omnicrm-migration.table_name.role_privileges'),
			'role_id', 'privilege_id'
		)->wherePivot('is_active', '=', 1)->where('privileges.is_active', 1);
	}
}
