<?php

namespace Modules\Core\Models\Lookup\Traits\Action;

/**
 * Class LookupValue Action
 */
trait LookupValueAction
{
    /**
     * Get Lookup Value by Key
     */
	public function getByKey(int $orgId, string $key)
	{
        return $this
            ->where('key', $key)
            ->where(function ($innerQuery) use ($orgId) {
                $innerQuery->where('org_id', $orgId)
                           ->orWhere('org_id', 0);
            })
            ->first();
    } //Function ends
    
} //Trait ends
