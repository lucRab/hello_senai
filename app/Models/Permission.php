<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Log;

class Permission extends Model
{
    protected $primaryKey = 'idpermissao';
    protected $table = 'permissao';
    protected $fillable = [
        'tipo',
        'idusuario',
        'idprojeto'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'idusuario');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'idprojeto');
    }
    
    use HasFactory;
}