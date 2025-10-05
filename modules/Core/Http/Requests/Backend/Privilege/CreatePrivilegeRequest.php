<?php

namespace Modules\Core\Http\Requests\Backend\Privilege;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class CreatePrivilegeRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.backend.privilege.create.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
