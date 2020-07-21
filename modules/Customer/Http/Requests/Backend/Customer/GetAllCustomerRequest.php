<?php

namespace Modules\Customer\Http\Requests\Backend\Customer;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class GetAllCustomerRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('omnicrm-validation.request_handler.backend.customer.getall.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
