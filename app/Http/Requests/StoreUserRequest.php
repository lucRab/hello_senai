<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
           'nome' => 'required|min:5|max:45',
           'email' => 'required|email|unique:usuario|max:255|regex:/ba.estudante.senai\.br/',
           'senha' => 'required|min:6|max:255'
        ];
    }
}
