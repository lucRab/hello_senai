<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UserResource extends JsonResource
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
        return [
            'id' => $this->idusuario,
            'nome' => $this->nome,
            'email' => $this->email,
            'apelido' => $this->apelido,
            'dataCriacao' => $formattedDate
        ];
    }
}