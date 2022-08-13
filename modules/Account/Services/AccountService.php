<?php

namespace Modules\Account\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Models\Organization\Organization;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Account\Repositories\AccountRepository;

use Modules\Core\Services\BaseService;

use Modules\Account\Events\AccountCreatedEvent;
use Modules\Account\Events\AccountUpdatedEvent;
use Modules\Account\Events\AccountDeletedEvent;

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
 * Class AccountService
 * @package Modules\Account\Services
 */
class AccountService extends BaseService
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
     * @var \Modules\Account\Repositories\AccountRepository
     */
    protected $accountRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \Modules\Account\Repositories\AccountRepository                   $accountRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        LookupValueRepository           $lookupRepository,
        AccountRepository               $accountRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->lookupRepository         = $lookupRepository;
        $this->accountRepository        = $accountRepository;
    } //Function ends


    /**
     * Get All Account for an Organization
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     *
     * @return mixed
     */
    public function getAll(string $orgHash, Collection $payload)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization details
            if ($user->hasRoles(config('aqveir.settings.default.role.key_super_admin'))) {
                //Get organization data
                $organization = $this->getOrganizationByHash($orgHash);
                $orgId = $organization['id'];
            } else {
                $orgId = $user['org_id'];
            } //End if

            //Assign to the return value
            $objReturnValue = $this->accountRepository
                ->where('org_id', $orgId)
                ->get();

        } catch(Exception $e) {
            throw $e;
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Show Account by Identifier
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \int $accountId
     *
     * @return mixed
     */
    public function show(string $orgHash, Collection $payload, int $accountId)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization details
            if ($user->hasRoles(config('aqveir.settings.default.role.key_super_admin'))) {
                //Get organization data
                $organization = $this->getOrganizationByHash($orgHash);
                $orgId = $organization['id'];
            } else {
                $orgId = $user['org_id'];
            } //End if

            //Assign to the return value
            $objReturnValue = $this->accountRepository
                ->where('id', $accountId)
                ->where('org_id', $orgId)
                ->first();

        } catch(Exception $e) {
            throw $e;
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Create Default Account
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \Modules\Core\Models\Organization\Organization $organization
     * 
     * @return mixed
     */
    public function createDefault(Collection $payload, Organization $organization) 
    {
        $objReturnValue=null;
        try {
            //Build data for default account
            $data = [
                'org_id' => $organization['id'],
                'name' => config('account.settings.new_organization.account.name'),
                'description' => config('account.settings.new_organization.account.default_text'),
                'email' => $organization['email'],
                'phone' => $organization['phone'],
                'type_key' => config('account.settings.new_organization.account.account_type'),
                'is_default' => true
            ];

            //Create default account
            $objReturnValue = $this->create($organization['hash'], collect($data), true);

        } catch(AccessDeniedHttpException $e) {
            log::error('AccountService:createDefault:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('AccountService:createDefault:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('AccountService:createDefault:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Create Account
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function create(string $orgHash, Collection $payload, bool $isAutoCreated=false)
    {
        $objReturnValue=null; $data=[]; $createdBy=0;
        try {

            //Auto Created Check
            if ($isAutoCreated) {
                //Build data
                $data = $payload->only([
                    'org_id',
                    'name', 'description', 
                    'email', 'phone'
                ])->toArray();
            } else {
                //Authenticated User
                $user = $this->getCurrentUser('backend');

                //Build data
                $data = $payload->only([
                    'name', 'description', 
                    'email', 'phone',
                ])->toArray();
                $data = array_merge($data, [
                    'org_id' => $user['org_id']
                ]);

                $createdBy = $user['id'];
            } //End if

            //Lookup data for Account Type
            $data['type_id'] = $this->getLookupValueId($data['org_id'], $payload, 'type_key', config('account.settings.new_organization.account.account_type'));

            //Create Account
            $account = $this->accountRepository->create($data, $createdBy);

            //Set Default status
            if (($payload->has('is_default')) && ($payload['is_default']==true)) {
                $this->accountRepository->setDefault($data['org_id'], $account['id']);
            } //End if
            $account->refresh();
                
            //Raise event: Account Created
            event(new AccountCreatedEvent($account, $isAutoCreated));                

            //Assign to the return value
            $objReturnValue = $account;

        } catch(AccessDeniedHttpException $e) {
            log::error('AccountService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('AccountService:create:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('AccountService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update Account
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \int $accountId
     *
     * @return mixed
     */
    public function update(string $orgHash, Collection $payload, int $accountId)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Build data
            $data = $payload->except(['is_default'])->toArray();

            //Lookup data for Account Type
            $data['type_id'] = $this->getLookupValueId($organization['id'], $payload, 'type_key', config('account.settings.new_organization.account.account_type'));

            //Update Account
            $account = $this->accountRepository->update($accountId, 'id', $data, $user['id']);

            //Set Default status
            if (($payload->has('is_default')) && ($payload['is_default']==true)) {
                $this->accountRepository->setDefault($organization['id'], $accountId);
            } //End if
            $account->refresh();
                
            //Raise event: Account Updated
            event(new AccountUpdatedEvent($account));                

            //Assign to the return value
            $objReturnValue = $account;

        } catch(AccessDeniedHttpException $e) {
            log::error('AccountService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('AccountService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('AccountService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Delete Account
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \int $accountId
     *
     * @return mixed
     */
    public function delete(string $orgHash, Collection $payload, int $accountId)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Get account data by account identifier
            $account = $this->accountRepository
                ->where('org_id', $organization['id'])
                ->where('id', $accountId)
                ->first();

            if (!empty($account)) {
                if ($account['is_default']) {
                    throw new BadRequestHttpException('Cannot delete default account.');
                } //End if

                //Delete Account
                $account = $this->accountRepository->delete($accountId, 'id', $user['id']);

                //Raise event: Account Deleted
                event(new AccountDeletedEvent($account));    
            } //End if

            //Assign to the return value
            $objReturnValue = $account;

        } catch(AccessDeniedHttpException $e) {
            log::error('AccountService:delete:AccessDeniedHttpException:' . $e->getMessage());
            throw $e;
        } catch(BadRequestHttpException $e) {
            log::error('AccountService:delete:BadRequestHttpException:' . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            log::error('AccountService:delete:Exception:' . $e->getMessage());
            throw $e;
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends