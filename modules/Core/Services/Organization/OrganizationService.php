<?php

namespace Modules\Core\Services\Organization;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Core\Repositories\Core\FileSystemRepository;

use Modules\Core\Services\BaseService;
use Modules\Core\Services\Role\RoleService;

use Modules\Core\Events\OrganizationCreatedEvent;
use Modules\Core\Events\OrganizationUpdatedEvent;
use Modules\Core\Events\OrganizationDeletedEvent;

use Modules\Core\Notifications\NewOrganizationWelcomeEmail;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile as File;
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
 * Class OrganizationService
 * 
 * @package Modules\Core\Services\Organization
 */
class OrganizationService extends BaseService
{
    /**
     * @var \Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var Modules\Core\Repositories\Lookup\LookupValueRepository
     */
    protected $lookupRepository;


    /**
     * @var Modules\Core\Repositories\Core\FileSystemRepository
     */
    protected $filesystemRepository;


    /**
     * @var \Modules\Core\Services\Role\RoleService
     */
    protected $roleService;


    /**
     * Service constructor.
     *
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \Modules\Core\Repositories\Core\FileSystemRepository              $filesystemRepository
     * @param \Modules\Core\Services\Role\RoleService                           $roleService
     */
    public function __construct(
        OrganizationRepository              $organizationRepository,
        LookupValueRepository               $lookupRepository,
        FileSystemRepository                $filesystemRepository,
        RoleService                         $roleService
    ) {
        $this->organizationRepository       = $organizationRepository;
        $this->lookupRepository             = $lookupRepository;
        $this->filesystemRepository         = $filesystemRepository;
        $this->roleService                  = $roleService;
    } //Function ends


    /**
     * Get all organization data
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isActive (optional)
     *
     * @return mixed
     * 
     */
    public function getAll(Collection $payload, bool $isActive=null)
    {
        $objReturnValue=null;
        try {
            $objReturnValue = $this->organizationRepository->getAllOrganizationsData();
                
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


    /**
     * Get organization data by identifier
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \string $hash
     * @param \bool $isActive (optional)
     *
     * @return mixed
     * 
     */
    public function getData(Collection $payload, string $hash, bool $isActive=null)
    {
        $objReturnValue=null;
        try {
            $organization = $this->organizationRepository->getOrganizationByHash($hash, $isActive);
              
            //return Organiztion object
            $objReturnValue = $organization;
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


    /**
     * Create organization
     * 
     * @param \Illuminate\Support\Collection $payload
     *
     * @return mixed
     * 
     */
    public function create(Collection $payload, string $ipAddress=null)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');
            if (empty($user)) { 
                throw new AccessDeniedHttpException();
            } //End if

            $keyIndustry = ($payload->has('industry_key'))?$payload['industry_key']:'industry_type_vanilla';
            $industry = $this->lookupRepository->getLookUpByKey(0, $keyIndustry);

            //Build Data
            $data = $payload->only([
                'name', 'subdomain', 'website',
                'contact_person_name', 'email', 'phone'
            ])->toArray();
            $data['industry_id'] = $industry['id'];

            //Create organization
            $organization = $this->organizationRepository->create($data);
            if (empty($organization)) {
                throw new BadRequestHttpException();
            } //End if

            //Clear organization cache
            $this->organizationRepository->setOrganizationClearCache();

            //Cashier Addition
            $options = [
                'metadata' => [
                    'org_hash'  => $organization['hash'],
                    'owner'     => $organization['contact_person_name']
                ]
            ];
            $stripeCustomer = $organization->createAsStripeCustomer($options);

            //Create default roles
            $roles = $this->roleService->createDefaultRole($organization['hash']);
            $organization['roles'] = $roles;

            //TODO: Create configuration data

            //Notify Organization Created
            $organization->notify(new NewOrganizationWelcomeEmail());

            //Raise event: New Organization Added
            event(new OrganizationCreatedEvent($organization, $payload));

            //return Organiztion object
            $objReturnValue = $organization;
                
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


    /**
     * Update Organization
     * 
     * @param \string $hash
     * @param \Illuminate\Support\Collection $payload
     *
     * @return mixed
     * 
     */
    public function update(string $hash, Collection $payload, File $file=null, string $ipAddress=null)
    {
        $objReturnValue=null;
        $industry=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');
            if (empty($user)) { 
                throw new AccessDeniedHttpException();
            } //End if

            //Refine request data based on user role
            if ($user->hasRoles(['super_admin'])) {
                $keyIndustry = ($payload->has('industry_key'))?$payload['industry_key']:'industry_type_vanilla';
                $industry = $this->lookupRepository->getLookUpByKey(0, $keyIndustry);

                //Build Data
                $data = $payload->toArray();
                $data['industry_id'] = $industry['id'];
            } else {
                //Build Data
                $data = $payload->except(['name', 'subdomain', 'industry_key', 'is_active'])->toArray();
            } //End if

            //Upload Logo, if exists
            if (!empty($file)) {
                $logo = $this->uploadImage($hash, $file, 'logo');
                $data['logo'] = $logo['file_path'];
            } //End if

            //Update data
            $organization = $this->organizationRepository->update($hash, 'hash', $data, $user['id']);
            if (empty($organization)) {
                throw new BadRequestHttpException();
            } //End if

            //Clear organization cache
            $this->organizationRepository->setOrganizationClearCache();

            //Cashier Update
            $options = [
                'metadata' => [
                    'org_hash'  => $organization['hash'],
                    'owner'     => $organization['contact_person_name']
                ]
            ];
            $stripeCustomer = null;
            if (empty($organization['stripe_id'])) {
                $stripeCustomer = $organization->createAsStripeCustomer($options);
            } else {
                $stripeCustomer = $organization->updateStripeCustomer($options);
            } //End if

            //Notify Organization Created
            $organization->notify(new NewOrganizationWelcomeEmail());

            //Raise event: Organization Updated
            event(new OrganizationUpdatedEvent($organization, $payload));

            //Return Organiztion object
            $objReturnValue = $organization;
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


    /**
     * Delete Organization
     * 
     * @param \string $hash
     * @param \Illuminate\Support\Collection $payload
     *
     * @return mixed
     * 
     */
    public function delete(string $hash, Collection $payload, string $ipAddress=null)
    {
        $objReturnValue=null;
        $industry=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');
            if (empty($user)) { 
                throw new AccessDeniedHttpException();
            } //End if

            $organization = $this->organizationRepository->getOrganizationByHash($hash);
            if (empty($organization)) {
                throw new BadRequestHttpException();
            } //End if

            //Update params & save
            $organization['is_active'] = false;
            $organization->save();

            //Delete data
            $organization = $this->organizationRepository->delete($hash, 'hash', $user['id']);
            if (empty($organization)) {
                throw new BadRequestHttpException();
            } //End if

            //Clear organization cache
            $this->organizationRepository->setOrganizationClearCache();

            //Raise event: Delete Organization
            event(new OrganizationDeletedEvent($organization));

            //Return Organiztion object
            $objReturnValue = $organization;           
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