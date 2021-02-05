<?php

namespace Modules\Preference\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Models\Organization\Organization;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Preference\Repositories\Meta\PreferenceMetaRepository;
use Modules\Preference\Repositories\Preference\PreferenceRepository;

use Modules\Core\Services\BaseService;

// use Modules\Preference\Events\PreferenceCreatedEvent;
// use Modules\Preference\Events\PreferenceUpdatedEvent;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class PreferenceService
 * @package Modules\Preference\Services
 */
class PreferenceService extends BaseService
{

    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var Modules\Core\Repositories\Lookup\LookupValueRepository
     */
    protected $lookupRepository;


    /**
     * @var \Modules\Preference\Repositories\Meta\PreferenceMetaRepository
     */
    protected $preferenceMetaRepository;


    /**
     * @var \Modules\Preference\Repositories\Preference\PreferenceRepository
     */
    protected $preferenceRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \Modules\Preference\Repositories\Meta\PreferenceMetaRepository    $preferenceMetaRepository
     * @param \Modules\Preference\Repositories\Preference\PreferenceRepository  $preferenceRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        LookupValueRepository           $lookupRepository,
        PreferenceMetaRepository        $preferenceMetaRepository,
        PreferenceRepository            $preferenceRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->lookupRepository         = $lookupRepository;
        $this->preferenceMetaRepository = $preferenceMetaRepository;
        $this->preferenceRepository     = $preferenceRepository;
    } //Function ends


    /**
     * Create Default Preferences
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $orgId
     * 
     * @return mixed
     */
    public function createDefault(Collection $payload, Organization $organization) 
    {
        $objReturnValue=null;
        try {
            $industry = $organization->industry()->first();
            $industryKey = (!empty($industry))?($industry['key']):'industry_type_vanilla';

            //Load preferences by industry type
            $preferences = $this->preferenceMetaRepository->getDataByIndustryType($industryKey);
            if (!empty($preferences)) {
                $data = [];
                foreach ($preferences as $key => $preference) {
                    //Preference lookup type
                    $preferencetType = $preference['type_key'];
                    $type = $this->lookupRepository->getLookUpByKey($organization['id'], $preferencetType);
                    if (empty($type))
                    {
                        throw new Exception('Unable to resolve the entity type');   
                    } //End if

                    $record = [
                        'org_id' => $organization['id'],
                        'key' => $preference['key'],
                        'display_value' => $preference['display_value'],
                        'description' => $preference['description'],
                        'is_minimum' => $preference['is_minimum'],
                        'is_maximum' => $preference['is_maximum'],
                        'is_multiple' => $preference['is_multiple'],
                        'keywords' => $preference['keywords'],
                        'order' => $preference['order'],
                        'type_id' => $type['id'],
                        'created_by' => 0
                    ];

                    array_push($data, $record);
                } //Loop ends

                //Create preferences data
                $objReturnValue = $this->create($organization['hash'], collect($data), true);
            } //End if

        } catch(AccessDeniedHttpException $e) {
            log::error('PreferenceService:createDefault:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('PreferenceService:createDefault:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('PreferenceService:createDefault:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends    


    /**
     * Create Preference
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function create(string $orgHash, Collection $payload, bool $isAutoCreated=false)
    {
        $objReturnValue=null;
        try {

            if ($isAutoCreated) {
                //Build data
                $data = $payload->toArray();
            } else {
                //Authenticated User
                $user = $this->getCurrentUser('backend');

                //Get organization details
                if ($user->hasRoles(config('crmomni.settings.default.role.key_super_admin'))) {
                    //Get organization data
                    $organization = $this->getOrganizationByHash($orgHash);
                    $orgId = $organization['id'];
                } else {
                    $orgId = $user['org_id'];
                } //End if

                //Build data
                $data = $payload->only([
                    'name', 'description', 
                    'email', 'phone',
                ])->toArray();
                $data = array_merge($data, [
                    'org_id' => $orgId, 
                    'created_by' => $user['id'] 
                ]);
            } //End if

            Log::info($payload);

            //Assign to the return value
            $objReturnValue = $this->preferenceRepository->savePreferences($data);

        } catch(AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            Log::error($e);
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends
