<?php

namespace OmarTaherSaad\StripePayments\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PerformPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|in:usd,eur',
            'order_uuid' => 'required|uuid',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'errors' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
