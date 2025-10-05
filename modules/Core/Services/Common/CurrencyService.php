<?php

namespace Modules\Core\Services\Common;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Common\CurrencyRepository;
use Modules\Core\Repositories\Common\CurrencyExternalRepository;

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
 * Class CurrencyService
 * 
 * @package Modules\Core\Services\Common
 */
class CurrencyService extends BaseService
{
    /**
     * @var \Modules\Core\Repositories\Common\CurrencyRepository
     */
    protected $currencyRepository;


    /**
     * @var \Modules\Core\Repositories\Common\CurrencyExternalRepository
     */
    protected $currencyExternalRepository;


    /**
     * CountryService constructor.
     *
     * @param \Modules\Core\Repositories\Common\CurrencyRepository              $currencyRepository
     * @param \Modules\Core\Repositories\Common\CurrencyExternalRepository      $currencyExternalRepository
     */
    public function __construct(
        CurrencyRepository                  $currencyRepository,
        CurrencyExternalRepository          $currencyExternalRepository
    ) {
        $this->currencyRepository           = $currencyRepository;
        $this->currencyExternalRepository   = $currencyExternalRepository;
    } //Function ends


    /**
     * Get All the Currency Data
     */
    public function index(bool $isActive=true)
    {
        $objReturnValue=null;
        try {
            $objReturnValue = $this->currencyRepository->getAllCurrencyData($isActive);
        } catch(AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update the Currency Exchange Rates 
     */
    public function updateCurrencyExchangeRates(string $base='USD')
    {
        $objReturnValue=null;
        try {
            //Get the currency exchange rates
            $response = $this->currencyExternalRepository->get('latest?base=USD');
            $rates = $response['rates'];

            if (!empty($rates)) {
                foreach ($rates as $key => $value) {

                    //Update the data
                    $currency = $this->currencyRepository->getCurrencyByCode($key);
                    $currency['fx_rate'] = $value;
                    $currency['updated_by'] = 0;
                    $currency->save();

                    $objReturnValue = $currency;
                } //Loop ends
            } else {
                throw new ModelNotFoundException();
            } //End if
        } catch(ModelNotFoundException $e) {
            throw new ModelNotFoundException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends