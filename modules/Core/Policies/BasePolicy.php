<?php

namespace Modules\Core\Policies;

use Illuminate\Support\Arr;
use Illuminate\Auth\Access\Response;

use Modules\Core\Models\Organization\Organization;

use Exception;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class BasePolicy
{

    public $organization = null;
    private $subdomain = null;


    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subdomain = Arr::first(explode('.', request()->getHost()));
        $this->organization = $this->getCurrentOrganization($this->subdomain);
    }


    /**
     * Check the URL Host name in request and return the OrgHash value.
     */
    public function getCurrentOrganization(string $subdomain, $user=null, bool $isForcedHostCheck=false) {
        try {
            $returnValue = null;
            $orgHashSubDomain=null;

            //SubDomain check
            if (!empty($subdomain) || $isForcedHostCheck) {
                $organization = Organization::where('subdomain', $subdomain)->firstOrFail();
                if (empty($organization)) {
                    throw new AccessDeniedHttpException();
                } //End if
            } //End if
            
            //Check user and domain conditions
            if (empty($user)) {
                $returnValue = $organization;
            } else {
                if (empty($orgHashSubDomain)) {
                    $returnValue = $user->organization['hash'];
                } else {
                    if ($user->organization['hash'] == $orgHashSubDomain) {
                        $returnValue = $orgHashSubDomain; 
                    } else {
                        throw new UnauthorizedHttpException();
                    } //End if
                } //End if
            } //End if
        } catch(Exception $e) {
            throw new AccessDeniedHttpException();
        } //Try-catch ends

        return $returnValue;
    } //Function ends

} //Class ends
