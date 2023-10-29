<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $author = $this->whenLoaded('user');
        return [
            'nomeProjeto' => $this->nome_projeto,
            'descricao' => $this->descricao,
            'dataCriacao' => $this->data_projeto,
            'status' => $this->status,
            'slug' => $this->slug,
            'autor' => ['nome' => $author->nome, 'apelido' => $author->apelido] 
        ];
    }
}