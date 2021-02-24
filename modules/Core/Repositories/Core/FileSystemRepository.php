<?php

namespace Modules\Core\Repositories\Core;

use Config;
use Exception;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile as File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
    public function upload(File $file, string $prefixPath=null, int $maxUploadFileSize=0)
    {
        $objReturnValue = null; 

        try {
            if ($file->isValid()) 
            {
                //Max upload file size in MB
                $MAX_UPLOAD_FILESIZE = ($maxUploadFileSize>0)?:(config('filesystems.upload_document_filesize')*(1024*1024));

                //Get File Related Info
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();

                if ($fileSize > $MAX_UPLOAD_FILESIZE) 
                { 
                    log::error('Document File Greater Than Configured File Size ');
                    throw new Exception('Document File Greater Than Configured File Size');                    
                } //End if                

                //Create File + Folder details
                $filenameToStore = config('filesystems.upload_folder');
                $filenameToStore .= empty($prefixPath)?'':('/' . $prefixPath);

                //Save in the directory, if dir is not exist it will created automatically.
                //$filePath = Storage::putFile($filenameToStore, $file, 'public');
                $filePath = $file->store($filenameToStore);

                //Send document object
                $objReturnValue=[];
                $objReturnValue['file_path'] = $filePath;
                $objReturnValue['file_name'] = $fileName;
                $objReturnValue['file_size'] = $fileSize;                 
            } //End if
        } catch(Exception $e) {
            $objReturnValue = null;
            throw new Exception($e->getMessage());      
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


} //Class ends