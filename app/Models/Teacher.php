<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class Teacher extends User
{
    public function createTeacher($data)
    {
        $idUser = parent::createUser($data);
        if(!DB::table('professor')->insert(['idusuario' => $idUser]))
        {
            return false;
        }
        return true;
    }
}