<?php

namespace Selectra_planilla\Http\Controllers;

use Illuminate\Http\Request;

use Selectra_planilla\Http\Requests;
use Selectra_planilla\Http\Controllers\Controller;

class FrontController extends Controller
{
    public function index()
    {
        return view('content.index');
    }
}
