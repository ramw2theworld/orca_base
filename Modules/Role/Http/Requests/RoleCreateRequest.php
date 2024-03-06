<?php

namespace Modules\Role\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleCreateRequest extends FormRequest
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
        $rules = [];
        if ($this->isMethod('post')) {
            $rules += [
                'name' => 'required|string|max:255',
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules += [
                'name' => 'sometimes|string|max:255'
            ];
        }

        return $rules;
    }
}
