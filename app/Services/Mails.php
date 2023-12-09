<?php
namespace App\Services;

use App\Mail\InviteEmail;
use App\Mail\LogEmail;
use App\Models\User;
use Mail;
use Carbon\Carbon;
/**
 * CLasse para fazer o envio de email
 */
class Mails
{
    /**
     * Método para enviar o email quando acontece algum erro
     *
     * @param string $subject
     * @param string $message
     * @return void
     */
    static public function sendLogError(string $subject,string $message) {
        Mail::to('guilherme.k.santos@ba.estudante.senai.br', 'Adiministrador')->send(new LogEmail([
             'fromName' =>'Error-Log',//define o nome do email que será enviado
             'fromEmail'=>'alert@gmail.com',//não funciona
             'subject'  => $subject,//define o assundo do email
             'message'  => [ 'message' => $message, 'mail'=> 'teste'],//define a mensagem do email
         ]));
    }
    /**
     *  Método para enviar um email quando um usario aceita um convite
     *
     * @param string $message
     * @param [type] $user
     * @param [type] $email
     * @param [type] $amail
     * @param [type] $name
     * @return void
     */
    static public function sendInvite(string $message, string $user, string $email, string $amail, string $name, $id) {
        Mail::to('guilherme.k.santos@ba.estudante.senai.br', 'Adiministrador')->send(new InviteEmail([
             'fromName' =>'Hello Senai',//define o nome do email que será enviado
             'fromEmail'=>'alert@gmail.com',//não funciona
             'subject'  => 'Convite Aceito',//define o assundo do email
             'message'  => ['message' => $message, 'amail' => $amail,'nome' =>$name, 'user' => $user, 'id' => $id, 'data' => Carbon::now()->format('d/m/Y')], //define a mensagem do email
         ])); 
    }
}