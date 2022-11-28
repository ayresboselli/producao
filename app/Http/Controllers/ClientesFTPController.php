<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientesFTPController extends Controller
{
    public function Clientes()
    {
        $clientes = DB::select("SELECT id, ftp_usuario, ftp_senha FROM clientes WHERE ftp_atualizado = 0");
        return $clientes;
    }
}
