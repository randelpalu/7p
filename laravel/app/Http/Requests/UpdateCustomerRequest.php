<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     description="Update Customer Request",
 *     title="UpdateCustomerRequest",
 *     required={"first_name", "last_name", "dob", "username", "password"},
 *     @OA\Property(property="first_name", type="string"),
 *     @OA\Property(property="last_name", type="string"),
 *     @OA\Property(property="dob", type="date"),
 *     @OA\Property(property="username", type="string"),
 *     @OA\Property(property="password", type="string"),
 * )
 */
class UpdateCustomerRequest extends ApiRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $customerId = $this->route('customer');

        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'dob' => 'required|date',
            'username' => [
                'required',
                'string',
                Rule::unique('customers')->ignore($customerId),
                'regex:/^[a-zA-Z0-9]+(?:[\.-][a-zA-Z0-9]+)?$/',
                'min:2'
            ],
            'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ];
    }
}
