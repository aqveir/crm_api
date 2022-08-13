<?php

namespace Modules\Core\Http\Requests\Backend\Lookup;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class FetchLookupRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.backend.lookup.fetch.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
