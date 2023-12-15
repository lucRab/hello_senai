<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AuthService
{

  public function isAdm(int $userId)
  {
        $userRule = DB::table('adm')->where('idusuario', $userId)->exists();
        if ($userRule) return true;
        return false;
  }

    public function isOwnerOfProject(int $userId)
    {
        $userRule = DB::table('acesso')->where('idusuario', $userId)->value('acesso');
        if ($userRule && $userRule == 'owner') return true;
        return false;
    }

    public function isOwnerOfInvite(int $userId)
    {
        $userRule = DB::table('convite')->where('idusuario', $userId)->exists();
        if ($userRule && $userRule == 'owner') return true;
        return false;
    }

    public function isTeacher(int $userId)
    {
        $userRule = DB::table('professor')->where('idusuario', $userId)->first();
        if ($userRule) return $userRule;
        return false;
    }

    public function abilities()
    {
        return ['project-store', 'project-update', 'project-destroy', 'invite-store', 'invite-update', 'invite-destroy'];
    }
}