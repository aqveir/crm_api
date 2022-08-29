<?php

namespace Modules\Contact\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContactImport implements ToArray, WithHeadingRow
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
    public function processDataArray(array $rows)
    {
        try {
            //Initialize the contacts array
            $contacts = [];
            
            //Iterate rows in the sheet
            foreach ($rows as $row) 
            {
                Log::info($row);
                //Set contact value
                $contact = $row;

                //Iterate columns in the row
                foreach ($row as $key => $value) {
                    //Handle email address 
                    if ($key=='email') {
                        $contact['details'] = [];
                        $contact['details']['type_key'] = 'contact_detail_type_email';
                        $contact['details']['subtype_key'] = 'contact_detail_subtype_email_personal';
                        $contact['details']['identifier'] = $row['email'];
                        $contact['details']['is_primary'] = true;
                    } //End if

                    //Handle phone number 
                    if ($key=='phone') {
                        $contact['details'] = [];
                        $contact['details']['type_key'] = 'contact_detail_type_phone';
                        $contact['details']['subtype_key'] = 'contact_detail_subtype_email_personal';
                        $contact['details']['identifier'] = $row['phone'];
                        $contact['details']['is_primary'] = true;
                    } //End if
                } //Loop ends

                array_push($contacts, $contact);
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