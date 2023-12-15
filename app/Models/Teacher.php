<?php

namespace App\Models;

use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;
use Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Teacher extends User
{
    protected $fillable = [
        'idusuario',
        'autenticado',
        'atualizado_em'
    ];
    protected $table = "professor";
    const UPDATED_AT = 'atualizado_em';

    public function challenge()
    {
        return $this->hasMany(Challenge::class, 'idusuario');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'idusuario');
    }

    public function createTeacher($idUser)
    {
        if(!DB::table('professor')->insert(['idusuario' => $idUser, 'autenticado' => 0])) {

            throw new HttpException(403, 'Não foi possível registrar um novo professor, tente novamente mais tarde');
        }
    }

    public function authorizeTeacher($idTeacher) 
    {
        if (!Teacher::where('idusuario', $idTeacher)->update(['autenticado' => 1])) {
            throw new HttpException(403, 'Não foi possivel autenticar o professor');
        }
    }

    public function getUnauthenticatedTeachers() {

    }
}