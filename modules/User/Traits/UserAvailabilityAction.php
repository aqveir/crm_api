<?php

namespace Modules\User\Traits;

use Config;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Action methods on User
 * 
 * @return \bool objReturnValue
 */
trait UserAvailabilityAction
{

    /**
     * Get Availability Status
     * 
     * @param \string $key 
     *
     * @return mixed
     */
    private function getStatusKey(string $key) {
        $objReturnValue = null; $statusKey = null;

        try {
            //Set Availability Status Key
            switch ($key) {
                case 'online':
                    $statusKey = 'user_status_online';
                    break;

                case 'away':
                    $statusKey = 'user_status_away';
                    break;
                
                default:
                    # code...
                    break;
            } //Switch ends

            //Assign to the return value
            $objReturnValue =  $statusKey;

        } catch(ExistingDataException $e) {
            throw new ExistingDataException();
        } catch(BadRequestHttpException $e) {
            log::error('UserService:register:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:register:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;

    } //Function ends

} //Trait ends