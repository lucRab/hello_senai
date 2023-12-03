<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectService implements ISlugService
{
    public function generateSlug($name)
    {
        $slug = DB::select("SELECT createUniqueSlug(?, ?) AS slug", [$name, 'projeto'])[0]->slug;
        return $slug;
    }
    
    public function getBySlug($slug)
    {
        $data = Project::with('user')->where('slug', '=', $slug)->first();
        if ($data) {
          $user = new User();
          $participants = $user
          ->join('permissao', 'permissao.idusuario', '=', 'usuario.idusuario')
          ->where('permissao.idprojeto', '=', $data->idprojeto)
          ->get(['nome', 'apelido']);
          if (!empty($participants)) {
            $data->participantes = $participants;
          }
          return $data;
        }
        
        return null;
    }
}