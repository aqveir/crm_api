<?php

namespace Modules\Contact\Http\Requests\Backend\Contact;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class GetContactRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('crmomni-validation.request_handler.backend.contact.index.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
