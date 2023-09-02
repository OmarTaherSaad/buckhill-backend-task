<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => [
                'required',
                'email',
                Rule::unique('users')->where(fn ($query) => $query->where('uuid', $this->route('user')))
            ],
            'password'      => ['required', 'confirmed', Password::defaults()],
            'address'       => 'required|string|max:255',
            'phone_number'  => 'required|string|max:255',
            'is_marketing'  => 'nullable|boolean',
            'avatar'        => 'nullable|uuid',
        ];
    }
}
