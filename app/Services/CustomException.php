<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class CustomExcepition {
    
    static function actionExcepition($index) {
        if(!$index)
        throw new \Exception('Não Foi Possível Realizar Essa Ação');
    }
    static function authorizedActionException(string $action, Auth $tokenUser, $inviatation = null) {
        if($inviatation == null) {
            if(!Auth::guard('sanctum')->check() && !$tokenUser->tokenCan($action)) {
                throw new \Exception('Unauthorized');
            }
        }else {
            if(!Auth::guard('sanctum')->check() && !$tokenUser->tokenCan($action) && !$tokenUser->apelido == $inviatation->user->apelido) {
                throw new \Exception('Unauthorized');
            }
        }
    }
}
