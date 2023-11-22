<?php

namespace App\Http\Controllers;

use App\Mail\LogEmail;
use Illuminate\Http\Request;
use Mail;

class LogEmailController extends Controller
{
    public function send() {
        var_dump('a');
        $send = Mail::to('l.rabelo@ba.estudante.senai.br', 'Lucas Rabelo')->send(new LogEmail([
             'fromName' =>'Log-Viewer',
             'fromEmail'=>'alert@gmail.com',
             'subject'=>'aaaaaaa',
           'message'=>'asdasdasdsad'
         ]));
      
        
    }
}
