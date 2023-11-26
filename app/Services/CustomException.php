<?php
namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
class CustomException {
    
    static function actionException($index,string $action = null) {
        if(!$index) {
            Log::error(self::class. ' Error', [$GLOBALS['request'], Auth::guard('sanctum')->user()]);
            Mails::sendLogError('Erro de ação', 'Erro gerado pelo usuario '.Auth::guard('sanctum')->user()['nome']. ' após tenta realizar a ação de '. $action);
            throw new \Exception('Não Foi Possível Realizar Essa Ação');
        }
        
    }
    static function authorizedActionException(string $action, $tokenUser,  $inviatation = null) {
        if(Auth::guard('sanctum')->check() ) {
            if($inviatation == null) {
                if(!$tokenUser->tokenCan($action)) { 
                    Mails::sendLogError('Erro de ação', 'Erro gerado pelo usuario '.Auth::guard('sanctum')->user()['nome']. ' após tenta realizar a ação de '. $action);
                    throw new \Exception('Unauthorized');
                }
            }else {
                if(!$tokenUser->tokenCan($action) && !$tokenUser->apelido == $inviatation->user->apelido) {   
                    Mails::sendLogError('Erro de ação', 'Erro gerado pelo usuario '.Auth::guard('sanctum')->user()['nome']. ' após tenta realizar a ação de '. $action);  
                    throw new \Exception('Unauthorized');
                }
            }
        }else {
            Mails::sendLogError('Erro de ação', 'Erro gerado pelo usuario '.Auth::guard('sanctum')->user()['nome']. ' após tenta realizar a ação de '. $action);
            throw new \Exception('Unauthorized');
        }
       
    }
}
