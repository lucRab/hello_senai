<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\DateService;

class InvitationResource extends JsonResource
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
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'dataCriacao' => DateService::transformDateHumanReadable($this->data_convite),
            'slug' => $this->slug,
            'autor' => ['nome' => $author->nome, 'apelido' => $author->apelido]
        ];
    }
}