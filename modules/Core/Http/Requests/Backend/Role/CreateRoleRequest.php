<?php

namespace Modules\Core\Http\Requests\Backend\Role;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.backend.role.create.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
