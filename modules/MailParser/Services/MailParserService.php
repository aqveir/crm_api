<?php

namespace Modules\MailParser\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Models\Organization\Organization;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\MailParser\Repositories\Exotel\VoiceCallRepository as ExotelVoiceCallRepository;
use Modules\MailParser\Repositories\Twilio\VoiceCallRepository as TwilioVoiceCallRepository;

use Modules\Core\Services\BaseService;

use Modules\MailParser\Events\Call\TelephonyCallCallbackReceivedEvent;
use Modules\MailParser\Events\Call\TelephonyCallInProgressEvent;
use Modules\MailParser\Events\Call\TelephonyCallCompletedEvent;
use Modules\MailParser\Events\Call\TelephonyCallNotConnectedEvent;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Modules\MailParser\Exceptions\TelephonyNoProviderException;
use Modules\MailParser\Exceptions\TelephonyConfigurationException;
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
     * @param \ExotelVoiceCallRepository                                        $exotelVoiceCallRepository
     * @param \TwilioVoiceCallRepository                                        $twilioVoiceCallRepository
     * 
     */
    public function __construct(
        OrganizationRepository              $organizationRepository,
        LookupValueRepository               $lookupRepository
        // ExotelVoiceCallRepository           $exotelVoiceCallRepository,
        // TwilioVoiceCallRepository           $twilioVoiceCallRepository
    ) {
        $this->organizationRepository       = $organizationRepository;
        $this->lookupRepository             = $lookupRepository;
        // $this->exotelVoiceCallRepository    = $exotelVoiceCallRepository;
        // $this->twilioVoiceCallRepository    = $twilioVoiceCallRepository;
    } //Function ends

} //Class ends