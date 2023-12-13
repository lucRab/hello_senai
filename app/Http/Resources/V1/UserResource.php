<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Services\AuthService;
use Illuminate\Support\Facades\Storage;

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
        $permission = $this->checkRole($this->idusuario);

        return [
            'id' => $this->idusuario,
            'nome' => $this->nome,
            'email' => $this->email,
            'apelido' => $this->apelido,
            'dataCriacao' => $formattedDate,
            'permissao' => $permission,
            'avatar' => $this->avatar ? Storage::url($this->avatar) : null
        ];
    }

    public function checkRole($userId) 
    {
        $service = new AuthService();
        if ($service->isAdm($userId)) return 'adm';
        else if ($service->isTeacher($userId)) return 'professor';
        else return 'aluno';
    }
}