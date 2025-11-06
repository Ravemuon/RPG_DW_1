<?php

namespace App\Http\Controllers;

use App\Models\Mestre;
use Illuminate\Http\Request;

class MestreController extends Controller
{
    public function index()
    {
        $mestres = Mestre::all();
        return view('mestres.index', compact('mestres'));
    }
}
