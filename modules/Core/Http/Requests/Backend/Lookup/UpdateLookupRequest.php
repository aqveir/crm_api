<?php

namespace App\Api\V1\Requests\Lookup;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class UpdateLookupRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.backend.lookup.update.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
