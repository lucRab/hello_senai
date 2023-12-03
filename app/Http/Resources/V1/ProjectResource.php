<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Services\DateService;

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
            'dataCriacao' => DateService::transformDateHumanReadable($this->data_projeto),
            'status' => $this->status,
            'slug' => $this->slug,
            'imagem' => Storage::url($this->imagem),
            'autor' => ['nome' => $author->nome, 'apelido' => $author->apelido],
            'participantes' => $this->participantes ?: []
        ];
    }
}