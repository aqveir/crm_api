<?php

namespace Modules\ServiceRequest\Http\Requests\Backend\Communication;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class CallOutgoingCommunicationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.backend.communication.call.outgoing.validation_rules');
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
