<?php

namespace Modules\Contact\Http\Requests\Backend\Contact;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class DeleteContactRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.backend.contact.delete.validation_rules');
    }


    public function authorize()
    {
        return true;
    }
    
} //Class ends
