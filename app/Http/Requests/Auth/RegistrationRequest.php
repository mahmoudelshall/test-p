<?php

namespace App\Http\Requests\Auth;

use App\Models\CountryCode;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            message(true, null, $validator->errors(), 422)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //$countryCodeId = request('country_code_id');
        // Find ISO from your country_codes table
       // $iso = CountryCode::find($countryCodeId)?->iso;

        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'code'  => 'required|string',
            'password' => [
                    'required',
                    'string',
                    'min:6',
                    'regex:/[a-z]/',      // at least one lowercase letter
                    'regex:/[A-Z]/',      // at least one uppercase letter
                    'regex:/\d/',         // at least one digit
                    'regex:/[@$!%*?&]/',  // at least one special character
                    'confirmed'
                ],
            // 'mobile' => [
            //     'required',
            //     'string',
            //     'phone:' . $iso, 
            //     Rule::unique('mobiles')->where(function ($query) {
            //         return $query
            //             ->where('country_code_id', $this->country_code_id)
            //             ->where('model_type', 'App\Models\User');
            //     }),
            // ],    
            // 'country_code_id' => 'required|exists:country_codes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'password.regex' => 'Password must include at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'password.min' => 'Password must be at least 6 characters.',
        ];
    }
}
