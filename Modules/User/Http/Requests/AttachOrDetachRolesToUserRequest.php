<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Database\data\ActionType;
use Illuminate\Validation\Rules\Enum;


class AttachOrDetachRolesToUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Adjust the authorization logic as needed
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "roles" => "required|array",
            "action" => ['required', new Enum(ActionType::class)]
        ];
    }
}
