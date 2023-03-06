<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class PersonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function commonRules(): array
    {
        $rules = [
            'name' => 'string|between:3,255',
            'contacts.*.contact_type' => 'in:landline,cellphone,email,whatsapp'
        ];

        if ($this->contacts) {
            foreach ($this->contacts as $k => $contact) {
                $rules["contacts.$k.contact"] = 'distinct|string';
                switch ($contact['contact_type']) {
                    case 'landline':
                        $rules["contacts.$k.contact"] .= '|size:10';
                        break;
                    case 'cellphone':
                        $rules["contacts.$k.contact"] .= '|size:11';
                        break;
                    case 'whatsapp':
                        $rules["contacts.$k.contact"] .= '|size:13';
                        break;
                    case 'email':
                        $rules["contacts.$k.contact"] .= '|email:filter';
                }
            }
        }

        return $rules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */

    public function messages()
    {
        return [
            'contacts.*.contact_type.in' => 'The contact type is invalid'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(
            response()->json(['data' => $errors], 422)
        );
    }
}
