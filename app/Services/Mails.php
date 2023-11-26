<?php
namespace App\Services;

use App\Mail\InviteEmail;
use App\Mail\LogEmail;
use App\Models\User;
use Mail;
/**
 * Classe para enviar alerta de email quando um erro for registrado no log
 */
class Mails
{
    static public function sendLogError(string $subject,string $message) {
        // $a = User::getEmailAdm();
        // var_dump($a);
        Mail::to('l.rabelo@ba.estudante.senai.br', 'Lucas Rabelo')->send(new LogEmail([
             'fromName' =>'Error-Log',
             'fromEmail'=>'alert@gmail.com',
             'subject'  => $subject,
             'message'  => [ 'message' => $message, 'mail'=> 'teste'],
         ]));

         Mail::to('lucasrabelo186@gmail.com', 'Lucas Rabelo')->send(new LogEmail([
            'fromName' =>'Error-Log',
            'fromEmail'=>'alert@gmail.com',
            'subject'  => $subject,
            'message'  => [ 'message' => $message, 'mail'=> 'teste'],
        ]));  
    }
    static public function sendInvite(string $message, $user, $email, $amail, $name) {
        $send = Mail::to($email, $user)->send(new InviteEmail([
             'fromName' =>'Hello Senai',
             'fromEmail'=>'alert@gmail.com',
             'subject'  => 'Convite Aceito',
             'message'  => ['message' => $message, 'amail' => $amail,'nome' =>$name, 'user' => $user],
         ]));
      
        
    }
}