<?php

namespace Modules\Customer\Http\Requests\Backend\Customer;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('omnicrm-validation.request_handler.backend.customer.update.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
