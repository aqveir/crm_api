<?php

namespace Modules\ServiceRequest\Http\Requests\Backend\Communication;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class SmsSendCommunicationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Config::get('crmomni-validation.request_handler.backend.communication.sms.send.validation_rules');
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

} //Class ends