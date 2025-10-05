<?php

namespace Modules\Core\Traits;

use Config;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Action methods on Lookup
 */
trait LookupAction
{

    /**
     * Get Lookup data based On Key
     *
     * @return objReturnValue
     */
    public function getLookupByKey(string $key, int $orgId=null)
    {
        $objReturnValue=null;
        try {
            $query = config('aqveir-class.class_model.lookup_value')::where('org_id', 0);

            if (!empty($orgId)) {
                $query = $query->orWhere('org_id', $orgId);
            } //End if
            
            $query = $query->where('key', $key);
            $query = $query->orderBy('id', 'asc');
            $query = $query->first();

            $objReturnValue = $query;  
        } catch (Exception $e) {
            log::error('LookupAction:getLookupByKey:Exception:' . $e->getMessage());
            $objReturnValue=null;
        } //Try-catch ends

        return $objReturnValue;
    } //End Function

} //Trait ends