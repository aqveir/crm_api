<?php

namespace Modules\Core\Traits;

use Config;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile as File;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Action methods on File Storage
 */
trait FileStorageAction
{

    /**
     * Upload File
     */
    public function uploadFile(string $orgHash, File $file, string $customPath=null)
    {
        $objReturnValue=null;
        $industry=null;
        try {
            $folderName=$orgHash;
            if (!empty($customPath)) {
                $folderName .= '/' . $customPath;
            } //End if

            //Save the file to the storage
            $objFileStore=$this->filesystemRepository->upload($file, $folderName);
            if (empty($objFileStore)) {
                throw new BadRequestHttpException();
            } //End if

            //Return Organiztion object
            $objReturnValue = $objFileStore;
        } catch(Exception $e) {
            Log::error($e);
            throw $e;
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Trait ends