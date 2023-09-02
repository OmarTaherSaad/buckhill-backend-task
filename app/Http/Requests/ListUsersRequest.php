<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'page'      => 'nullable|integer|min:1',
            'limit'     => 'nullable|integer|between:1,100',
            'sortBy'    => 'nullable|string|in:created_at,updated_at,shipped_at,amount',
            'desc'      => 'nullable|boolean',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email'     => 'nullable|email',
            'phone'     => 'nullable|string|max:255',
            'address'   => 'nullable|string|max:255',
            'created_at' => 'nullable|date',
            'marketing' => 'nullable|boolean',
        ];
    }
}
