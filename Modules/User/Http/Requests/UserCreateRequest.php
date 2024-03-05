<?php

namespace Modules\User\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Core\Traits\RespondsWithJson;
use Illuminate\Validation\Rule;


class UserCreateRequest extends FormRequest
{
    use RespondsWithJson;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userId = $this->user ? $this->user->id : null;
        $rules = [];

        if ($this->isMethod('post')) {
            $rules += [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'role_id' => 'required|integer',
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules += [
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
                'password' => 'sometimes|required|string|min:8|confirmed',
                'role_id' => 'sometimes|required|integer',
            ];
        }

        return $rules;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, $this->sendError('Error occurred while creating user.', $validator->errors()));
    }
}
