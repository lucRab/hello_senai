<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class Teacher extends User
{
    public function createTeacher($data)
    {
        $idUser = parent::createUser($data);
        if(DB::table('professor')->insert(['idusuario' => $idUser]))
        {
            return response()->json(['success' => 'Professor Inserido Com Sucesso', 200]);
        }
        return response()->json(['error' => 'Falha ao Inserir', 403]);
    }
}