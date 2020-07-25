<?php

namespace Modules\Core\Repositories\Core;

use Config;
use Exception;
use Carbon\Carbon;

use Illuminate\Http\Request;
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
    public function upload(Request $request, string $orgHash, string $folderName=null, int $uploadfilesize=0)
    {
        $objReturnValue = null; 

        try {
            if ($request->hasFile('document')) 
            {
                //Max upload file size in MB
                $MAX_UPLOAD_FILESIZE = (($uploadfilesize>0)?:(config('filesystems.upload_document_filesize')))*(1024*1024);

                //Get Document Related Info
                $documentName = $request->document->getClientOriginalName();
                $documentSize = $request->file('document')->getClientSize();

                if ($documentSize > $MAX_UPLOAD_FILESIZE) 
                { 
                    log::error('Document File Greater Than Configured File Size ');
                    throw new Exception('Document File Greater Than Configured File Size');                    
                } //End if                

                //Create File + Folder details
                $filenameToStore = config('filesystems.upload_folder');
                $filenameToStore .= '/' . $orgHash;
                $filenameToStore .= (empty($folderName))?:('/' . $folderName);

                //Save in the directory, if dir is not exist it will created automatically.
                $urlDocument = Storage::putFile($filenameToStore, $request->file('document'), 'public');

                //Set Bucket and Folder For Upload Documents
                //$s3_bucket = config('omnichannel.settings.document_file.s3_upload_bucket'); 
                //$s3_folder = config('omnichannel.settings.document_file.s3_upload_folder'); 

                // //Upload File to S3
                // if(Storage::disk('s3')->has($s3_folder.'/'.$orgId)) {
                //     Storage::disk('s3')->put($s3_folder.'/'.$orgId.'/'.$doc_name, fopen($request->file('document'), 'r+'), 'public');
                //     $urlDocument = Storage::disk('s3')->url($s3_bucket.'/'.$s3_folder.'/'.$orgId.'/'.$doc_name);
                // } else {
                //     Storage::disk('s3')->put($s3_folder.'/'.$orgId.'/'.$doc_name, fopen($request->file('document'), 'r+'), 'public');
                //     $urlDocument = Storage::disk('s3')->url($s3_bucket.'/'.$s3_folder.'/'.$orgId.'/'.$doc_name);
                // } //End if-else  

                $objReturnValue = $urlDocument;                 
            } //End if
        } catch(Exception $e) {
            $objReturnValue = null;
            throw new Exception($e->getMessage());      
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


} //Class ends