<?php

namespace Modules\Core\Repositories\Core;

use Config;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile as File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class FileSystemRepository.
 */
class FileSystemRepository
{
    /**
     * Function to Save Uploaded Documents to storage
     *
     * @return objReturnValue
     */
    public function upload(File $file, string $prefixPath=null, int $maxUploadFileSize=0, bool $keepOriginalExtension=true)
    {
        $objReturnValue = null; $filePath = null;

        try {
            if ($file->isValid()) 
            {
                //Max upload file size in MB
                $MAX_UPLOAD_FILESIZE = ($maxUploadFileSize>0)?:(config('filesystems.upload_document_filesize', 5)*(1024*1024));

                //Get File Related Info
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileSize = $file->getSize();

                if ($fileSize > $MAX_UPLOAD_FILESIZE) 
                { 
                    log::error('Document File Greater Than Configured File Size ');
                    throw new Exception('Document File Greater Than Configured File Size');                    
                } //End if                

                //Create File + Folder details
                $filePath = config('filesystems.upload_folder');
                $filePath .= empty($prefixPath)?'':('/' . $prefixPath);

                //Save in the directory, if dir is not exist it will created automatically.
                //$filePath = Storage::putFile($filePath, $file, 'public');
                if ($keepOriginalExtension) {
                    $fullFileName = \Str::random(40) . '.' . $fileExtension;
                    $filePath = $file->storeAs($filePath, $fullFileName);
                } else {
                    $filePath = $file->store($filePath);
                } //End if
                
                if (empty($filePath)) {
                    throw new BadRequestHttpException();
                } //End if

                //Send document object
                $objReturnValue=[];
                $objReturnValue['file_path'] = $filePath;
                $objReturnValue['file_name'] = $fileName;
                $objReturnValue['file_extn'] = $fileExtension;
                $objReturnValue['file_size'] = $fileSize;                 
            } //End if
        } catch(Exception $e) {
            $objReturnValue = null;
            throw $e;      
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


} //Class ends