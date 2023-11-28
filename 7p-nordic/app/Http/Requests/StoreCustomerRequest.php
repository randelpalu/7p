<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     description="Store Customer Request",
 *     title="StoreCustomerRequest",
 *     required={"first_name", "last_name", "dob", "username", "password"},
 *     @OA\Property(property="first_name", type="string"),
 *     @OA\Property(property="last_name", type="string"),
 *     @OA\Property(property="dob", type="date"),
 *     @OA\Property(property="username", type="string"),
 *     @OA\Property(property="password", type="string"),
 * )
 */
class StoreCustomerRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'dob' => 'required|date',
            'username' => 'required|regex:/^[a-zA-Z0-9]+(?:[\.-][a-zA-Z0-9]+)?$/|unique:customers',
            'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ];
    }
}
