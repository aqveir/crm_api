<?php

namespace App\Api\V1\Requests\Organization;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('crmomni-validation.request_handler.organization.update.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
