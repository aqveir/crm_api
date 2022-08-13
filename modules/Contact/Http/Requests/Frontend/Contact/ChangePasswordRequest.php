<?php

namespace Modules\Contact\Http\Requests\Frontend\Contact;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.auth.change_password.validation_rules');
    }

    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
