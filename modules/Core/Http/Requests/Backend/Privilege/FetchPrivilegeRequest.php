<?php

namespace Modules\Core\Http\Requests\Backend\Privilege;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class FetchPrivilegeRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.backend.privilege.fetch.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
