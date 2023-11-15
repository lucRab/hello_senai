<?php
namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
class CustomException {
    
    static function actionException($index) {
        if(!$index)
        throw new \Exception('Não Foi Possível Realizar Essa Ação');
    }
    static function authorizedActionException(string $action, $tokenUser, string $method, $inviatation = null) {
        if(Auth::guard('sanctum')->check() ) {
            if($inviatation == null) {
                if(!$tokenUser->tokenCan($action)) {
                    Log::error('Unauthorized',['idusuario: '    => Auth::guard('sanctum')->id(),
                                                'método: '      => $method]);
                    throw new \Exception('Unauthorized');
                }
            }else {
                if(!$tokenUser->tokenCan($action) && !$tokenUser->apelido == $inviatation->user->apelido) {   
                    Log::error('Unauthorized');
                    throw new \Exception('Unauthorized');
                    
                }
            }
        }else {
            Log::error('Unauthorized');
            throw new \Exception('Unauthorized');
        }
       
    }
}
