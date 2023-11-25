<?php
namespace App\Services;

use App\Mail\LogEmail;
use Mail;
/**
 * Classe para enviar alerta de email quando um erro for registrado no log
 */
class Mails
{
    static public function sendLogError(string $subject,string $message) {
        $send = Mail::to('l.rabelo@ba.estudante.senai.br', 'Lucas Rabelo')->send(new LogEmail([
             'fromName' =>'Error-Log',
             'fromEmail'=>'alert@gmail.com',
             'subject'  => $subject,
             'message'  => $message
         ]));
      
        
    }
    static public function sendInvite(string $subject,string $message, $user, $email) {
        $send = Mail::to($user, $email)->send(new LogEmail([
             'fromName' =>'Error-Log',
             'fromEmail'=>'alert@gmail.com',
             'subject'  => 'Convite Aceito',
             'message'  => $message
         ]));
      
        
    }
}