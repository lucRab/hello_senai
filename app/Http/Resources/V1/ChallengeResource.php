<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\DateService;
use App\Models\User;

class ChallengeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = User::where('idusuario', '=', $this->idusuario)->first();
        return [
            'desafio' => [
                'titulo' => $this->titulo, 
                'descricao' => $this->descricao, 
                'dataCriacao' => DateService::transformDateHumanReadable($this->data_convite), 
                'slug' => $this->slug,
               'autor' => ['nome' => $user->nome, 'apelido' => $user->apelido]
            ]
        ];
    }
}