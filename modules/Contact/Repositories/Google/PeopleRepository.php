<?php

namespace Modules\Contact\Repositories\Google;

use Modules\Contact\Models\Contact\Contact;

use Google\Client;
use Google\Service\Poeple;

/**
 * Class PeopleRepository
 * 
 * @package Modules\Contact\Repositories\Contact
 */
class PeopleRepository
{
    protected $service=null;

    /**
     * Repository constructor.
     *
     * @param  Contact  $model
     */
    public function __construct(Contact $model)
    {
        $client = new Google\Client();
        $client->setApplicationName("Client_Library_Examples");
        $client->setDeveloperKey("YOUR_APP_KEY");

        $this->service = new Google\Service\Poeple($client);
    }


    /**
     * Validate Contact Username
     * 
     * @param  int  $orgId
     * @param  string  $username
     *
     * @return boolean
     */
    public function list()
    {
        $objReturnValue = false;
        try {
            $optParams = array('filter' => 'free-ebooks');
            $data = $this->service
                ->volumes->listVolumes('Henry David Thoreau', $optParams);

            //Record exists
            $objReturnValue = !empty($data);            
        } catch(Exception $e) {
            throw new Exception($e);
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Get Full Customers Data for an Organization (Backend)
     * 
     * @param  int     $orgId
     * @param  string  $orgHash
     * 
     * @return $contact
     */
    public function getFullData(int $orgId, string $orgHash=null, bool $isForcedDB=false, int $page=1, int $size=10)
    {
        $objReturnValue=null;
        try {
            if (!$isForcedDB) {
                //TODO: Get elastic data

            } //End if            

            // Get data from DB
            if (empty($objReturnValue)) {
                $objReturnValue = $this->getFullDataFromDB($orgId, $page, $size);
            } //End if
        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get Full Customers Data from DB for an Organization (Backend)
     * 
     * @param  int     $orgId
     * 
     * @return $contact
     */
    private function getFullDataFromDB(int $orgId, int $page=1, int $size=10)
    {
        $objReturnValue=null;
        try {
            $contact = $this->model
                ->where('org_id', $orgId)
                ->skip(($page - 1) * $size)
                ->take($size)
                ->get();

            $objReturnValue = $contact;
        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get Full Data for a Cutomer by Identifier
     * 
     * @param  int     $orgId
     * @param  string  $hash
     * @param  bool    $isForcedDB
     * 
     * @return $objReturnValue
     */
    public function getFullDataByIdentifier(int $orgId, string $hash, bool $isForcedDB=false)
    {
        $objReturnValue=null;
        try {
            if (!$isForcedDB) {

            } //End if

            if (empty($objReturnValue)) {
                $objReturnValue = $this->getFullDataByIdentifierFromDB($orgId, $hash);
            } //End if
        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get Full Data by DB for a Cutomer by Identifier
     * 
     * @param  int     $orgId
     * @param  string  $hash
     * 
     * @return $contact
     */
    private function getFullDataByIdentifierFromDB(int $orgId, string $hash)
    {
        $objReturnValue=null;
        try {
            $contact = $this->model
                ->where('org_id', $orgId)
                ->where('hash', $hash)
                ->first();

            $objReturnValue = $contact;
        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
