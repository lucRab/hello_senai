<?php

namespace App\Models;

use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;
use Log;
/**
 * Classe 
 */
class Teacher extends User
{
    public function createTeacher($data)
    {
        $idUser = parent::createUser($data);
        if(!DB::table('professor')->insert(['idusuario' => $idUser])) {
            Log::error(self::class. "Error Create", ['dados: ' => $data, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
            return false;
        }
        return true;
    }
}