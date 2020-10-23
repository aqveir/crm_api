<?php

namespace Modules\Contact\Http\Requests\Backend\Contact;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class GetAllContactRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('crmomni-validation.request_handler.backend.contact.getall.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
