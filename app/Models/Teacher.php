<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Log;

class Teacher extends User
{
    public function createTeacher($data)
    {
        $idUser = parent::createUser($data);
        if(!DB::table('professor')->insert(['idusuario' => $idUser])) {
            Log::error(self::class. "Error Create", ['dados: ' => $data,
            'browser' => $_SERVER["HTTP_USER_AGENT"],
            'URI' => $_SERVER["REQUEST_URI"],
            'Server' => $_SERVER["SERVER_SOFTWARE"]]);
            return false;
        }
        return true;
    }
}