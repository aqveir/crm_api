<?php

namespace App\Api\V1\Requests\Lookup;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class LookupRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('omnicrm-validation.request_handler.get_lookup_value.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
