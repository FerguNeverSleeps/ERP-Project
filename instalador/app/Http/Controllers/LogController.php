<?php

namespace Selectra_planilla\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use Redirect;
use Selectra_planilla\Http\Requests;
use Selectra_planilla\Http\Requests\LoginRequest;
use Selectra_planilla\Http\Controllers\Controller;

class LogController extends Controller
{
    public function index()
    {
    	return view('usuario.login');
    }
    public function store(LoginRequest $request)
    {
    	if(Auth::attempt(['email'=> $request['email'], 'password' =>$request['password']]))
        {
            return Redirect::to('usuario');
        }else{
            $request->session()->flash('message-error','Datos incorrectos');
            //Session::flash('message-error','Datos incorrectos');
            return Redirect::to('/');
        }
    }
}
