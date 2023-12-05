<?php

namespace App\Http\Requests;

use Auth;
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
        $user = Auth::guard('sanctum')->user();
        $rules = [
            'nome' => 'required|min:5|max:45',
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/ba.estudante.senai\.br/',
                Rule::unique('usuario')->ignore($user->idusuario, 'idusuario')
            ],
            'apelido' => [
                'required',
                'min:6',
                'max:255',
                Rule::unique('usuario')->ignore($user->idusuario, 'idusuario')
            ]
        ];
        return $rules;
    }
}