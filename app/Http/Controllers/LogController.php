<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
	public function Index()
    {
        $sql = "SELECT * FROM log 
                WHERE created_at BETWEEN DATE_SUB(NOW(), INTERVAL 1 DAY) AND NOW()
                ORDER BY id DESC";

        $logs = DB::select($sql);

        return view('log', ['logs' => $logs]);
    }
}
