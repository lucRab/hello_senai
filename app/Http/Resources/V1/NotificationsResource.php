<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class NotificationsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->whenLoaded('user');
        $carbonDate = Carbon::parse($this->data_envio);
        $formattedDate = $carbonDate->format('d/m/Y');

        return [
            'mensagem' => $this->texto,
            'enviadoEm' => $formattedDate,
            'remetente' => ['nome' => $user->nome, 'apelido' => $user->apelido]
        ];
    }
}
