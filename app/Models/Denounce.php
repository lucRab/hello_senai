<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denounce extends Model
{
    use HasFactory;
    protected $primaryKey = 'iddenuncia';
    protected $table = 'denuncia';
    protected $fillable = [
        'texto',
        'idprojeto'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'idprojeto', 'idprojeto');
    }
}