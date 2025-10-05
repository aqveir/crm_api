<?php

namespace Modules\Contact\Http\Requests\Backend\Contact;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.backend.contact.update.validation_rules');
    }


    public function authorize()
    {
        return true;
    }

} //Class ends
