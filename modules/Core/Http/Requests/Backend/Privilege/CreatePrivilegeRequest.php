<?php

namespace App\Api\V1\Requests\Privilege;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class CreatePrivilegeRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('crmomni-validation.request_handler.create_privileges.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
