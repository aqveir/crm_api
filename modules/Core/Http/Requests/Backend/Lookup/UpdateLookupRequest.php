<?php

namespace App\Api\V1\Requests\Lookup;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class UpdateLookupRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('omnicrm-validation.request_handler.lookup.update.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
