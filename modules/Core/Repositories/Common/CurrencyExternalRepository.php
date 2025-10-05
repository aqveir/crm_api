<?php

namespace Modules\Core\Repositories\Common;

use Config;

use Modules\Core\Repositories\Core\ExternalRepository;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;


/**
 * Class CurrencyExternalRepository
 * 
 * @package Modules\Core\Repositories\Common
 */
class CurrencyExternalRepository extends ExternalRepository
{
    /**
     * Repository constructor.
     *
     */
    public function __construct()
    {
        $this->baseUri = config('omnichannel.settings.external_data.currency_exchange.base_uri');
        $this->timeout = config('omnichannel.settings.external_data.currency_exchange.timeout');
    }

} //Class ends
