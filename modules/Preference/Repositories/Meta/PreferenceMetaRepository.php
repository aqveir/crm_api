<?php

namespace Modules\Preference\Repositories\Meta;

use Modules\Preference\Contracts\{PreferenceMetaContract};

use Modules\Preference\Models\Meta\PreferenceMeta;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class PreferenceMetaRepository
 * @package Modules\Preference\Repositories\Meta
 */
class PreferenceMetaRepository extends EloquentRepository implements PreferenceMetaContract
{

    /**
     * Repository constructor.
     *
     * @param  PreferenceMeta  $model
     */
    public function __construct(PreferenceMeta $model)
    {
        $this->model = $model;
    }


    /**
     * Get Meta Preferences By Industry Key
     * 
     * @param  string  $industryKey
     *
     * @return array
     */
    public function getDataByIndustryType(string $industryKey)
    {
        $objReturnValue = null;
        try {
            $data = $this->model
                ->where('industry_key', $industryKey)
                ->orderBy('order')
                ->get();

            //Record exists
            $objReturnValue = !empty($data)?$data:null;            
        } catch(Exception $e) {
            throw new Exception($e);
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends
