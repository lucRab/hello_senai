<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;
    protected $fillable = [
        'idusuario',
        'destinatario',
        'texto',
        'status'
    ];
    
    protected $table = "registro_email";
    protected $primaryKey = "idemail";
    const CREATED_AT = "data_envio";
    const UPDATED_AT = null;

    public function sender()
    {
        return $this->belongsTo(User::class, 'idusuario', 'idusuario');
    }

    public function addressee()
    {
        return $this->belongsTo(User::class, 'destinatario', 'idusuario');
    }
}