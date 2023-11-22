<?php
namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
class CustomException {
    
    static function actionException($index) {
        if(!$index) {
            Log::error(self::class. ' Error', [$GLOBALS['request'], Auth::guard('sanctum')->user()]);
            
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
