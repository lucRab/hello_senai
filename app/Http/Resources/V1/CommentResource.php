<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\DateService;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->whenLoaded('user');

        $data = [
            'idcomentario' => $this->idcomentario,
            'texto' => $this->texto,
            'criadoEm' => DateService::transformDateHumanReadable($this->criado_em),
            'resposta' => CommentResource::collection($this->replies)
        ];

        if ($this->relationLoaded('user')) {
            $data['usuario'] = ['nome' => $user->nome, 'apelido' => $user->apelido, 'avatar' => $user->avatar ? Storage::url($user->avatar) : null];
        }

        return $data;
    }
}