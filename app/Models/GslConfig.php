<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GslConfig extends Model
{
    use HasFactory;
    protected $fillable = ['funcao', 'ativo'];
}
