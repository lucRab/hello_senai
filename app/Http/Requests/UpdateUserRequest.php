<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $rules = [
            'nome' => 'required|min:5|max:45',
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:usuario',
                'regex:/ba.estudante.senai\.br/'
            ],
            'senha' => 'required|min:6|max:255'
        ];

        if ($this->method() === 'PUT')
        {
            $rules['senha'] = 'nullable|min:6|max:100';
            $rules['email'] = [
                'required',
                'email',
                'max:255',
                Rule::unique('usuario')->ignore($this->id)
            ];
        }
        
        return $rules;
    }
}
