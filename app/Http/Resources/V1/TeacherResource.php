<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $carbonDate = Carbon::parse($this->data_criacao);
        $formattedDate = $carbonDate->format('d/m/Y');
        $user = $this->whenLoaded('user');
        $challenges = $this->whenLoaded('challenge');

        $data = [
            'nome' => $user->nome,
            'apelido' => $user->apelido,
            'avatar' => $user->avatar ? Storage::url($user->avatar) : null,
            'dataCriacao' => $formattedDate,
        ];

        if ($this->relationLoaded('challenge')) {
            $data['desafios'] = $this->challenge ? ChallengeResource::collection($this->challenge) : [];
        }

        return $data;
    }
}