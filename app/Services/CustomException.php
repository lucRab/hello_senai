<?php
namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
class CustomException {
    
    static function actionException($index) {
        if(!$index) {
            Log::error(self::class. ' Error', [   'action' => $index,
            'browser' => $_SERVER["HTTP_USER_AGENT"],
            'URI' => $_SERVER["REQUEST_URI"],
            'Server' => $_SERVER["SERVER_SOFTWARE"]]);
            
            throw new \Exception('Não Foi Possível Realizar Essa Ação');
        }
        
    }
    static function authorizedActionException(string $action, $tokenUser,  $inviatation = null) {
        if(Auth::guard('sanctum')->check() ) {
            if($inviatation == null) {
                if(!$tokenUser->tokenCan($action)) {  
                    throw new \Exception('Unauthorized');
                }
            }else {
                if(!$tokenUser->tokenCan($action) && !$tokenUser->apelido == $inviatation->user->apelido) {     
                    throw new \Exception('Unauthorized');
                }
            }
        }else {
            throw new \Exception('Unauthorized');
        }
       
    }
}
