<?php

namespace Modules\Contact\Http\Requests\Frontend\Auth;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class ContactSocialCallbackRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.frontend.contact.social_login_callback.validation_rules');
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
