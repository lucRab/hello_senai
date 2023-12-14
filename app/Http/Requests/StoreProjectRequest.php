<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'nomeProjeto' => 'required|min:3|max:80',
            'descricao' => 'required',
            'github' => 'required|regex:/github.com/',
            'projetoStatus' => 'required',
            'imagem' => 'required|image|max:1024',
            'participantes' => 'nullable',
            'desafio' => 'nullable'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'nome_projeto' => $this->nomeProjeto,
            'projeto_status' => $this->projetoStatus
        ]);
    }
}