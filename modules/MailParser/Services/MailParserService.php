<?php

namespace Modules\MailParser\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Models\Organization\Organization;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;

use Modules\Core\Services\BaseService;

use Modules\MailParser\Events\MailReceivedEvent;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Modules\MailParser\Exceptions\MailParseNoProviderException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class MailParserService
 * @package Modules\MailParser\Services
 */
class MailParserService extends BaseService
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
     * @var \VoiceCallRepository
     */
    protected $exotelVoiceCallRepository;
    protected $twilioVoiceCallRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * 
     */
    public function __construct(
        OrganizationRepository              $organizationRepository,
        LookupValueRepository               $lookupRepository
    ) {
        $this->organizationRepository       = $organizationRepository;
        $this->lookupRepository             = $lookupRepository;
    } //Function ends


    /**
     * Process the incoming Mail request
     * 
     * @param \string $orgHash
     * @param \string $provider
     * @param \Illuminate\Support\Collection $payload
     * @param \string $ipAddress (optional)
     *
     * @return mixed
     */
    public function processMailData(string $orgHash, string $provider, Collection $request, Collection $payload, string $ipAddress=null)
    {
        $objReturnValue=null; $response=null;
        try {
            //Create lock file
            $fileName = './mem.lock';
            $fp = fopen($fileName, 'w+');

            //Lock the file
            flock($fp, LOCK_EX); 

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Convert payload to Array
            $data = $payload->toArray();
            $data = array_merge($data, [
                'created_by' => $request['remote']
            ]);

            switch ($provider) {
                case 'zapier': //Zapier
                    break;
                
                default:
                    throw new MailParseNoProviderException();
                    break;
            } //End switch
                
            //Raise event
            event(new MailReceivedEvent($organization, $request, $payload, $ipAddress));

            //Assign to the return value
            $objReturnValue = $data;

        } catch(MailParseNoProviderException $e) {
            log::error('MailParserService:processMailData:MailParseNoProviderException:' . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            log::error('MailParserService:processMailData:Exception:' . $e->getMessage());
            throw $e;
        }  finally {
			//Release lock
			flock($fp, LOCK_UN);
			fclose($fp); //Unlock the file
		} //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends