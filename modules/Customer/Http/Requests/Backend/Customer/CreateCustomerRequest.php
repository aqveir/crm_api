<?php

namespace Modules\Customer\Http\Requests\Backend\Customer;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class CreateCustomerRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('crmomni-validation.request_handler.backend.customer.create.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
