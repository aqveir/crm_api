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
     * @param \Modules\Account\Repositories\AccountRepository                     $accountRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        LookupValueRepository           $lookupRepository,
        AccountRepository                $accountRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->lookupRepository         = $lookupRepository;
        $this->accountRepository         = $accountRepository;
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
                'account_type' => config('account.settings.new_organization.account.account_type'),
                'is_default' => true,
                'created_by' => 0
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
        $objReturnValue=null; $data=[];
        try {
            if ($isAutoCreated) {
                //Build data
                $data = $payload->only([
                    'org_id',
                    'name', 'description', 
                    'email', 'phone', 'created_by'
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
                    'org_id' => $user['org_id'], 
                    'created_by' => $user['id'] 
                ]);
            } //End if

            //Lookup data
            $accountType = $payload['account_type'];
            $lookupEntity = $this->lookupRepository->getLookUpByKey($data['org_id'], $accountType);
            if (empty($lookupEntity))
            {
                throw new Exception('Unable to resolve the entity type');   
            } //End if
            $data['type_id'] = $lookupEntity['id'];

            //Create Account
            $account = $this->accountRepository->create($data);
            $account->load('type', 'owner');
                
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
     * @param \Illuminate\Support\Collection $payload
     * @param \int $accountId
     *
     * @return mixed
     */
    public function update(Collection $payload, int $accountId)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Build data
            $data = $payload->only(['note'])->toArray();

            //Update Account
            $note = $this->accountRepository->update($noteId, 'id', $data, $user['id']);
                
            //Raise event: Account Updated
            event(new NoteUpdatedEvent($note));                

            //Assign to the return value
            $objReturnValue = $note;

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
     * @param \Illuminate\Support\Collection $payload
     * @param \int $accountId
     *
     * @return mixed
     */
    public function delete(Collection $payload, int $accountId)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get Account
            $note = $this->accountRepository->getById($noteId);

            //Delete Account
            $response = $this->accountRepository->deleteById($noteId, $user['id']);
            if ($response) {
                //Raise event: Account Deleted
                event(new NoteDeletedEvent($note));
            } //End if
            
            //Assign to the return value
            $objReturnValue = $response;

        } catch(AccessDeniedHttpException $e) {
            log::error('AccountService:delete:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('AccountService:delete:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('AccountService:delete:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends