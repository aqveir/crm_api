<?php

namespace Modules\Core\Models\Organization\Traits\Action;

use Config;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Exceptions\DuplicateException;

/**
 * Action methods on User Role
 */
trait OrganizationAction
{

    /**
     * Get Organization Configurations Based On Key
     *
     * @return objReturnValue
     */
    public function getOrganizationConfigurationByKey(string $configKey)
    {
        $objReturnValue=null;
        try {
            //Get all configurations
            $configurations = $this->configurations()->get();

            if (!empty($configurations)) {
                $objReturnValue = collect($configurations)->firstWhere('key', $configKey);                
            } //End if   
        } catch (Exception $e) {
            log::error('OrganizationAction:getOrganizationConfigurationByKey:Exception:' . $e->getMessage());
            $objReturnValue=null;
        } //Try-catch ends

        return $objReturnValue;
    } //End Function





    /**
     * Get User Organization Settings 
     *
     * @return objReturnValue
     */
    public function getOrganizationSettings(int $orgId)
    {   
        $objReturnValue=null;
        try {
            $userOrg = config('org_setting.settings');
            $userAccess = $userOrg[(string) $orgId];
 
            $objReturnValue = $userAccess;    
        } catch (Exception $e) {
            Log::error(json_encode($e));
            $objReturnValue=null;
        } //Try-catch ends

        return $objReturnValue;
    } //End Function

    /**
     * Get Organization Setting Based On Key
     *
     * @return objReturnValue
     */
    public function getOrganizationSettingByKey($orgId, $key)
    {
        $objReturnValue=null;
        try {
            $userOrg = config('portiqo-crm-org-config.settings');
            $userAccess = $userOrg[(string) $orgId];
            $orgSetting = $userAccess[(string) $key];
            //log::debug($orgSetting);

            $objReturnValue = $orgSetting;    
        } catch (Exception $e) {
            Log::error(json_encode($e));
            $objReturnValue=null;
        } //Try-catch ends

        return $objReturnValue;
    } //End Function

    /**
     * Update Organization By Id
     *
     * @return objReturnValue
     */
    public function updateOrganization(int $id, int $user_id, $request)
    {
        $objReturnValue=false;
        try {
            $isDuplicate = $this->checkDuplicateOrganization($id, $request);

            if($isDuplicate!=null) {
                throw new DuplicateException('Organization Name Already Exist');      
            } else {
                $query = config('aqveir-class.class_model.organization')::where('id', $id);
                $query = $query->update([
                            'name'=>$request['name'],
                            'modified_by' => $user_id,
                            'modified_on' => Carbon::now()
                        ]);

                $objReturnValue=true;
            } //End if-else 

        } catch (DuplicateException $e) {
            throw new DuplicateException('Organization Name Already Exist');     
        } catch (Exception $e) {
            Log::error(json_encode($e));
            $objReturnValue=false;
        } //Try-catch ends

        return $objReturnValue;
    } //End Function

    /**
     * Check isDuplicate Organization
     *
     * @return objReturnValue
     */
    private function checkDuplicateOrganization(int $id, $request)
    {
        $objReturnValue=null;
        try {
            $query = config('aqveir-class.class_model.organization')::where('name', $request['name']);
            $query = $query->whereNotIn('id', [$id]);
            $query = $query->firstOrFail();

            $objReturnValue=$query;
        } catch (Exception $e) {
            Log::error(json_encode($e));
            $objReturnValue=null;
        } //Try-catch ends

        return $objReturnValue;
    } //End Function
} //Trait ends