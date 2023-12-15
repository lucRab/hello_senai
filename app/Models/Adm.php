<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Adm extends User
{
    protected $fillable = [
        'idusuario',
        'atualizado_em',
    ];
    protected $table = 'adm';
    const CREATED_AT = NULL;
    const UPDATED_AT = 'atualizado_em';

    public function createAdm($idUser) 
    {
        $now = Carbon::now();
        if(!Adm::create(['idusuario' => $idUser, 'atualizado_em' => $now])) {
            throw new HttpException(403, 'Não foi possível registrar um novo adm, tente novamente mais tarde');
        }
    }
}