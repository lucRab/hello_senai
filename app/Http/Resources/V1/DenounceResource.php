<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class DenounceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $project = $this->whenLoaded('project');
        $carbonDate = Carbon::parse($this->criada_em);
        $formattedDate = $carbonDate->format('d/m/Y');

        return [
            'id' => $this->iddenuncia,
            'texto' => $this->texto,
            'projeto' => $project->slug,
            'criada_em' => $formattedDate
        ];
    }
}