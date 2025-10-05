<?php

namespace Modules\Contact\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContactImportExcel implements ToArray, WithHeadingRow
{
    use Importable;

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
    public function processDataArray($organization, array $sheets)
    {
        try {
            //Initialize the contacts array
            $contacts = [];

            //Iterate Worksheets
            foreach ($sheets as $key => $value) {
                $rows = $value;
            
                //Iterate rows in the sheet
                foreach ($rows as $row) 
                {
                    //Set contact value
                    $contact = $row;

                    //Iterate columns in the row
                    foreach ($row as $key => $value) {
                        //Handle email address 
                        if ($key=='email') {
                            $details = [];
                            $details['type_key'] = 'contact_detail_type_email';
                            $details['subtype_key'] = 'contact_detail_subtype_email_personal';
                            $details['identifier'] = $row['email'];
                            $details['is_primary'] = true;

                            if (isset($contact['details'])) {
                                array_push($contact['details'], $details);
                            } else {
                                $contact['details'] = [];
                                array_push($contact['details'], $details);
                            } //End if
                        } //End if

                        //Handle phone number 
                        if ($key=='phone') {
                            $details = [];
                            $details['type_key'] = 'contact_detail_type_phone';
                            $details['subtype_key'] = 'contact_detail_subtype_phone_mobile';
                            $details['identifier'] = $row['phone'];
                            $details['is_primary'] = true;

                            if (isset($contact['details'])) {
                                array_push($contact['details'], $details);
                            } else {
                                $contact['details'] = [];
                                array_push($contact['details'], $details);
                            } //End if
                        } //End if
                    } //Loop ends

                    array_push($contacts, $contact);
                } //Loop ends
            } //Loop ends

            return $contacts;

        } catch(Exception $e) {
            throw $e;
        } //Try-Catch ends
    } //Function ends
    

    /**
     * The row heading set to 1
     */
    public function headingRow(): int
    {
        return 1;
    } //Function ends

} //Class ends