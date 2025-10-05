<?php

namespace Modules\Core\Http\Requests\Backend\Lookup;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class CreateLookupRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.backend.lookup.create.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
