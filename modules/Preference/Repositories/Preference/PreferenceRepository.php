<?php

namespace Modules\Preference\Repositories\Preference;

use Log;
use Modules\Preference\Contracts\{PreferenceContract};

use Modules\Preference\Models\Preference\Preference;
use Modules\Core\Repositories\EloquentRepository;

use Exception;

/**
 * Class PreferenceRepository
 * @package Modules\Preference\Repositories\Preference
 */
class PreferenceRepository extends EloquentRepository implements PreferenceContract
{

    /**
     * Repository constructor.
     *
     * @param  Preference  $model
     */
    public function __construct(Preference $model)
    {
        $this->model = $model;
    }


    /**
     * Create Preferences by array
     * 
     * @param  array  $data
     *
     * @return array
     */
    public function createPreferences(Array $records)
    {
        $objReturnValue = null;
        try {
            $data = [];

            //Iterate all records in the array
            foreach ($records as $record) {
                //Create preference data
                $preference = $this->createPreference($record);

                //Create response array collection
                array_push($data, $preference);
            } //Loop ends

            //Record exists
            $objReturnValue = !empty($data)?$data:null;            
        } catch(Exception $e) {
            throw new Exception($e);
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Create Preference
     * 
     * @param  object  $payload
     *
     * @return object
     */
    public function createPreference($payload)
    {
        $objReturnValue = null;
        try {
            //Create preference
            $preference = $this->model->create($payload);

            //Create data values
            if (array_key_exists('data',$payload)) {
                if (($payload['data']!=null) && (is_array($payload['data'])) && (count($payload['data']) > 0)) {
                    $dataObj = $preference->data()->create($payload['data']);

                    //Associate data with preference
                    $preference->data()->associate($dataObj)->save();

                    $values = $payload['data']['values'];
                    foreach ($values as $value) {
                        $dataObj->values()->create((array)$value);
                    } //Loop ends
                } //End if
            } //End if

            //Record exists
            $objReturnValue = $preference;            
        } catch(Exception $e) {
            throw new Exception($e);
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update Preference by Identifier
     * 
     * @param  int  $id
     * @param  object  $payload
     *
     * @return object
     */
    public function updatePreferences(int $id, $payload, $user)
    {
        $objReturnValue = null;
        try {
            //Get existing preference data
            $preferenceBefore = $this->model->where('id', $id)->first();
            if (empty($preferenceBefore)) {
                throw new Exception(400);
            } //End if

            //Update preference
            $preference = $this->model->update($id, 'id', $payload, $user['id']);

            //Modily the data relationships
            if ((!empty($preferenceBefore['type'])) && 
                ($preferenceBefore['type']['key']=='data_type_lookup')){

                //Clear the old data if the type varies OR data varies
                if (($preferenceBefore['type_id'] != $preference['type_id']) ||
                    (array_key_exists('data',$payload) && 
                     preferenceBefore['data']['name'] != $payload['data']['name'])) {

                    //Delete the preference data
                    $preference->data->values->delete();
                    $preference->data->delete();

                    //Remove association
                    $preference->data()->dissociate()->save();

                //Update the data 
                } elseif(($preferenceBefore['type_id'] == $preference['type_id']) &&
                    (array_key_exists('data',$payload) && 
                    (preferenceBefore['data']['name'] == $payload['data']['name']))
                ) {
                    //Do something
                } else {
                    //Do nothing
                } //End if
            } //End if

            //Create data values
            if (array_key_exists('data',$payload)) {
                if (($payload['data']!=null) && (is_array($payload['data'])) && (count($payload['data']) > 0)) {
                    $dataObj = $preference->data()->create($payload['data']);

                    //Associate data with preference
                    $preference->data()->associate($dataObj)->save();

                    $values = $payload['data']['values'];
                    foreach ($values as $value) {
                        $dataObj->values()->create((array)$value);
                    } //Loop ends
                } //End if
            } //End if

            //Record exists
            $objReturnValue = $preference;            
        } catch(Exception $e) {
            throw new Exception($e);
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends
