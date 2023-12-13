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
        $participants = $this->whenLoaded('participants');
        $comments = $this->whenLoaded('comments');

        $data = [
            'nomeProjeto' => $this->nome_projeto,
            'descricao' => $this->descricao,
            'dataCriacao' => DateService::transformDateHumanReadable($this->data_projeto),
            'status' => $this->status,
            'slug' => $this->slug,
            'status' => ucfirst($this->status),
            'imagem' => Storage::url($this->imagem),
            'autor' => ['nome' => $author->nome, 'apelido' => $author->apelido, 'avatar' => $author->avatar ? Storage::url($author->avatar) : null],
            'comentarios' => CommentResource::collection($comments) ?: []
        ];

        if ($this->relationLoaded('participants')) {
            $data['participantes'] = $this->participants ? $this->filterParticipantData($this->participants) : [];
        }

        return $data;
    }

    public function filterParticipantData($arr)
    {
        $filterData = $arr->map(function ($participant) {
            return [
                'nome' => $participant->nome,
                'apelido' => $participant->apelido,
                'avatar' => $participant->avatar ? Storage::url($participant->avatar) : null
            ];
        });
        return $filterData;
    }
}