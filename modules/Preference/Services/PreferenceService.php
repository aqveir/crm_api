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
                foreach ($preferences as $key => $preference) {
                    Log::info($preference);
                } //Loop ends
            } //End if

            //Create defult user
            // $user = $this->create(collect($data), $orgId, true);
            // if (empty($user)) {
            //     throw new BadRequestHttpException();
            // } //End if

            // //Store Additional Settings
            // $user['is_active'] = true;
            // $user['is_pool'] = true;
            // $user['is_default'] = true;
            // if ($user->save()) {
            //     throw new HttpException(500);
            // } //End if

            //Assign to the return value
            //$objReturnValue = $user;

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
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            if ($user->hasRoles(config('crmomni.settings.default.role.key_super_admin'))) {
                //Get organization data
                $organization = $this->getOrganizationByHash($orgHash);
                $orgId = $organization['id'];
            } else {
                $orgId = $user['org_id'];
            } //End if

            //Build user data
            $data = $payload->only([
                'username', 'password', 'email', 'phone', 
                'first_name', 'last_name', 'is_remote_access_only'
            ])->toArray();

            // Duplicate check
            $isDuplicate=$this->userRepository->exists($data['username'], 'username');
            if (!$isDuplicate) {
                //Add Organisation data
                $data = array_merge($data, [ 'org_id' => $orgId, 'created_by' => $user['id'] ]);

                //Create User
                $user = $this->userRepository->create($data);

                //Raise event: User Added
                event(new UserCreatedEvent($user, $isAutoCreated));
            } else {
                throw new BadRequestHttpException();
            } //End if

            //Assign to the return value
            $objReturnValue = $user;

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
