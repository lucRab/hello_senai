<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;
    protected $fillable = [
        'idusuario',
        'idusuario_convite',
        'texto',
        'status'
    ];
    
    protected $table = "registro_email";
    protected $primaryKey = "idemail";
    const CREATED_AT = "data_envio";
    const UPDATED_AT = null;

    public function user()
    {
        return $this->belongsTo(User::class, 'idusuario', 'idusuario');
    }
}
