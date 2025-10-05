<?php

namespace Modules\Core\Services\Common;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Common\CountryRepository;

use Modules\Core\Services\BaseService;

use Illuminate\Http\Request;
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
 * Class CountryService
 * 
 * @package Modules\Core\Services\Common
 */
class CountryService extends BaseService
{
    /**
     * @var \Modules\Core\Repositories\Common\CountryRepository
     */
    protected $countryrepository;


    /**
     * CountryService constructor.
     *
     * @param \Modules\Core\Repositories\Common\CountryRepository    $countryrepository
     */


    public function __construct(
        CountryRepository               $countryrepository
    ) {
        $this->countryrepository        = $countryrepository;
    } //Function ends


    public function index()
    {
        $objReturnValue=null;
        try {
            
            //Get country data
            $objReturnValue = $this->countryrepository->getAllCountryData();

        } catch(AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends