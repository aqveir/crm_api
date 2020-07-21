<?php

namespace App\Api\V1\Requests\Organization;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class CreateOrganizationRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('omnicrm-validation.request_handler.organization.create.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
