<?php

namespace Modules\Preference\Repositories\Preference;

use Modules\Preference\Contracts\{PreferenceContract};

use Modules\Preference\Models\Preference\Preference;
use Modules\Core\Repositories\EloquentRepository;

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
     * Save Preferences by array
     * 
     * @param  array  $data
     *
     * @return array
     */
    public function savePreferences(Array $payload)
    {
        $objReturnValue = null;
        try {
            $data = $this->model
                ->insert($payload);

            //Record exists
            $objReturnValue = !empty($data)?$data:null;            
        } catch(Exception $e) {
            throw new Exception($e);
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends
