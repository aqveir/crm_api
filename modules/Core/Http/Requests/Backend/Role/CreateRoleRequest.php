<?php

namespace App\Api\V1\Requests\Role;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('crmomni-validation.request_handler.create_role.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
