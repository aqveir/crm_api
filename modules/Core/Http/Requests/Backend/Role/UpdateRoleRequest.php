<?php

namespace App\Api\V1\Requests\Role;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('omnicrm-validation.request_handler.edit_role.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
