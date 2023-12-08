<?php
namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
/**
 * Classe para define exeções customizadas
 */
class CustomException {
    /**
     * Método para definir exeção de ação realizada pelo usuario
     *
     * @param [type] $index
     * @param string|null $action
     * @return void
     */
    static function actionException($index,string $action = null) {
        //verifica se a ação não gerou um erro
        if(!$index) {
            //salva no log o erro causadi
            Log::error(self::class. ' Error', [$GLOBALS['request'], Auth::guard('sanctum')->user()]);
            //envia para o email do administrador 
            Mails::sendLogError('Erro de ação', 'Erro gerado pelo usuario '.Auth::guard('sanctum')->user()['nome']. ' após tenta realizar a ação de '. $action);
            //abre uma execeção do erro
            throw new \Exception('Não Foi Possível Realizar Essa Ação');
        }
        
    }
    /**
     * Método para definir exceção de autorização
     *
     * @param string $action
     * @param [type] $tokenUser
     * @param [type] $inviatation
     * @return void
     */
    static function authorizedActionException(string $action, $tokenUser,  $inviatation = null) {
        if(Auth::guard('sanctum')->check() ) {
            if($inviatation == null) {
                //verifica se o usuario tem autorização para realizar essa ação
                if(!$tokenUser->tokenCan($action)) { 
                    var_dump("teste");
                    throw new \Exception('Unauthorized');
                }
            }else {
                 //verifica se o usuario tem autorização para realizar essa ação e se o usuario é o mesmo que criou
                if(!$tokenUser->tokenCan($action) && !$tokenUser->apelido == $inviatation->user->apelido) {   
                    throw new \Exception('Unauthorized');
                }
            }
        }else {
            //abre uma execeção do erro
            throw new \Exception('Unauthorized');
        }
       
    }
}
