<?php

namespace Modules\ServiceRequest\Http\Requests\Backend\Task;

use Config;
use Modules\Boilerplate\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Config::get('aqveir-validation.request_handler.backend.task.update.validation_rules');
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
