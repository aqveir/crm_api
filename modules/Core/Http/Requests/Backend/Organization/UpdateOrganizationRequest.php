<?php

namespace Modules\Core\Http\Requests\Backend\Organization;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.backend.organization.update.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
