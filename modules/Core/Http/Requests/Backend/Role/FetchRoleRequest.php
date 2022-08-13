<?php

namespace Modules\Core\Http\Requests\Backend\Role;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class FetchRoleRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.backend.role.fetch.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
