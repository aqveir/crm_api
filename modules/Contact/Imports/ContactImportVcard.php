<?php

namespace Modules\Contact\Imports;

use Config;
use Carbon\Carbon;

use Sabre\VObject;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ContactImportVcard
{

    /**
     * Process the collection object
     */
    public function collection(Collection $rows)
    {
        return $this->processDataArray($rows->toArray());
    } //Function ends


    /**
     * Process the array object
     */
    public function array(array $rows)
    {
        return $this->processDataArray($rows);
    } //Function ends


    /**
     * Process Data Array
     * 
     * @return $contacts
     */
    public function processDataArray($organization, $vcards, string $type='contact_type_customer'): array
    {
        try {
            //Initialize the contacts array
            $contacts = [];

            //Iterate the VCards
            if ($vcards) {
                while($vcard = $vcards->getNext()) {

                    //Set contact value
                    $contact = [];
                    $contact['org_id'] = $organization['id'];

                    //Contact Name
                    $fullName = (string)$vcard->FN;
                    $contact['first_name'] = $fullName;
                    $contact['middle_name'] = $fullName;
                    $contact['last_name'] = $fullName;

                    //Contact Type
                    $contact['type'] = $type;

                    //Contact Avatar
                    if ($vcard->PHOTO) {
                        $contact['avatar'] = (string)$vcard->PHOTO;
                    } //End if

                    //Contact DOB
                    if ($vcard->BDAY) {
                        $contact['birth_at'] = Carbon::parse((string)$vcard->BDAY,'UTC')->format(config('aqveir.settings.date_format_response_generic'));
                    } //End if

                    //Contact gender
                    if ($gender = (string)$vcard->NOTE) {
                        switch ($gender) {
                            case 'Gender: Male':
                                $contact['contact_gender']='contact_gender_male';
                                break;
                            
                            case 'Gender: Female':
                                $contact['contact_gender']='contact_gender_female';
                                break;
                            
                            default:
                            $contact['contact_gender']='contact_gender_others';
                                break;
                        } //Switch ends
                    } //End if
                    
                    //Iterate columns for Phone Number
                    if ($vcard->TEL) {
                        $contact['details']=[];
                        $isPrimary=false;
                        foreach ($vcard->TEL as $tel) {   
                            $details = [];
                            $details['type_key'] = 'contact_detail_type_phone';
                            switch ($tel['TYPE']) {
                                case 'HOME':
                                    $details['subtype_key'] = 'contact_detail_subtype_phone_landline';
                                    break;

                                case 'CELL':
                                default:
                                    $details['subtype_key'] = 'contact_detail_subtype_phone_mobile';
                                    if (!$isPrimary) {
                                        $details['is_primary'] = true;
                                        $isPrimary=true;
                                    } //End if
                                    break;
                            } //Switch ends
                            $details['identifier'] = (string)$tel;

                            array_push($contact['details'], $details);
                        } //Loop ends                        
                    } //End if

                    //Iterate for Email Address
                    if ($vcard->EMAIL) {
                        $contact['details']=[];
                        $isPrimary=false;
                        foreach ($vcard->EMAIL as $email) {   
                            $details = [];
                            $details['type_key'] = 'contact_detail_type_email';
                            switch ($email['TYPE']) {
                                case 'INTERNET,WORK':
                                case 'WORK':
                                    $details['subtype_key'] = 'contact_detail_subtype_email_work';
                                    break;

                                case 'INTERNET,HOME':
                                case 'HOME':
                                default:
                                    $details['subtype_key'] = 'contact_detail_subtype_email_personal';
                                    if (!$isPrimary) {
                                        $details['is_primary'] = true;
                                        $isPrimary=true;
                                    } //End if
                                    break;
                            } //Switch ends
                            $details['identifier'] = (string)$email;

                            array_push($contact['details'], $details);
                        } //Loop ends                        
                    } //End if

                    //Add the VCard to the contacts notes for future refernce
                    $contact['notes'] = [];
                    array_push($contact['notes'], [
                        'org_id' => $organization['id'],
                        'entity_type' => 'entity_type_contact',
                        'note' => $vcard->serialize()
                    ]);
   
                    array_push($contacts, $contact);
                } //loop ends
            } //End if

            return $contacts;
        } catch(Exception $e) {
            throw $e;
        } //Try-Catch ends
    } //Function ends


    /**
     * @param  string|UploadedFile|null  $filePath
     * @param  string|null  $disk
     * @param  string|null  $readerType
     * @return array
     *
     * @throws BadRequestHttpException
     */
    public function parse($filePath = null, string $disk = null, string $readerType = null)
    {
        $filePath = $this->getFilePath($filePath);
        $returnValue = null;

        try {
            if (file_exists($filePath)) {
                $data = file_get_contents($filePath);

                if (!$data) {
                    throw new BadRequestHttpException('ERROR_FILE_DATA');
                } //End if

                //Read the Vcards data from the file stream.
                $vcards = new VObject\Splitter\VCard($data);
                if (!$vcards) {
                    throw new BadRequestHttpException('ERROR_VCARD_DATA');
                } //End if
                $returnValue = $vcards;
            } else {
                $returnValue = null;
            } //End if
        } catch(Exception $e) {
            throw $e;
        } //Try-Catch ends

        return $returnValue;
    } //Function ends


    /**
     * @param  UploadedFile|string|null  $filePath
     * @return UploadedFile|string
     *
     * @throws NoFilePathGivenException
     */
    private function getFilePath($filePath = null)
    {
        $filePath = $filePath ?? $this->filePath ?? null;

        if (null === $filePath) {
            throw new BadRequestHttpException('ERROR_FILE_MISSING');
        } //End if

        return $filePath;
    } //Function ends

} //Class ends