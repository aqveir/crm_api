<?php

namespace Modules\CloudTelephony\Http\Requests\Exotel;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class VoiceCallbackRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.telephony.exotel.voice.callback.validation_rules');
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
