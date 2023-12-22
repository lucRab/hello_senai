<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class NotificationsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sender = $this->whenLoaded('sender');
        $carbonDate = Carbon::parse($this->data_envio);
        $formattedDate = $carbonDate->format('d/m/Y');

        $data = [
            'mensagem' => $this->texto,
            'enviadoEm' => $formattedDate,
            'remetente' => ['nome' => $sender->nome, 'apelido' => $sender->apelido, 'avatar' => $sender->avatar ? Storage::url($sender->avatar) : null],
            'mensagem' => $this->mensagem
        ];

        if ($this->relationLoaded('addressee')) {
            $data['destinatario'] = ['nome' => $this->addressee->nome, 'apelido' => $this->addressee->apelido, 'avatar' => $this->addressee->avatar ? Storage::url($this->addressee->avatar) : null];
        }

        return $data;
    }
}