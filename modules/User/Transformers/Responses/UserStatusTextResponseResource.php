<?php

namespace Modules\User\Transformers\Responses;

use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

use Exception;

class UserStatusTextResponseResource extends ResourceCollection
{
    private $output;
    private $phoneFormat;


    public function __construct($collection, string $output, string $phoneFormat)
    {
       parent::__construct($collection);
       $this->output = $output;
       $this->phoneFormat = $phoneFormat;
    }


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $objReturnValue=null;

        try {
            $output = explode(",", (!empty($this->output))?$this->output:'hash,full_name,phone,email');

            //Process Output
            if (!empty($output)) {
                $objReturnValue = [];
                
                //Process collection
                foreach ($this->collection as $data) {
                    $response = '';
                    foreach ($output as $value) {
                        if ($value=='phone') {
                            $country = (!empty($data['country']))?$data['country']['phone_idd_code']:'';

                            $phone = $this->phoneFormat;
                            $phone = str_replace('[country]', $country, $phone);
                            $phone = str_replace('[number]', $data['phone'], $phone);

                            $response .= $phone;
                        } else {
                            $response .= $data[$value];
                        } //End if
                    } //Loop ends

                    array_push($objReturnValue, $response);
                } //Loop ends
            } //End if
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
