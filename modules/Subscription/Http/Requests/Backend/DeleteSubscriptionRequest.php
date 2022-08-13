<?php

namespace Modules\Subscription\Http\Requests\Backend;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class DeleteSubscriptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.backend.subscription.delete.validation_rules');
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
