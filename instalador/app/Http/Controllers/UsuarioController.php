<?php

namespace Selectra_planilla\Http\Controllers;

use Selectra_planilla\Http\Requests;
use Selectra_planilla\Http\Requests\UserCreateRequest;
use Selectra_planilla\Http\Controllers\Controller;
use Selectra_planilla\User;
use Session;
use Redirect;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
    	$users = User::paginate(5);
        return view('usuario.index',compact('users'));
    }
    public function create()
    {
    	return view('usuario.create');
    }
    public function store(Request $request)
    {
        User::create($request->all());
        return redirect('/usuario')->with('message','store');
    }
    public function show($id)
    {
    	$user = User::find($id);
        return view('usuario.permisos',['user'=>$user]);
    }
    public function edit($id)
    {
    	$user = User::find($id);
        return view('usuario.edit',['user'=>$user]);
    }
    public function update($id, Request $request)
    {
    	$user = User::find($id);
        $user->fill($request->all());
        $user->save();
        Session::flash('message','Usuario Editado Correctamente');
        return Redirect::to('/usuario');
    }
    public function destroy($id)
    {
    	$user=User::find($id);
        $user->delete();
        Session::flash('message','Usuario Eliminado Correctamente');
        return Redirect::to('/usuario');
    }
    public function permisos($id, Request $request)
    {
        echo $id;
        /*$user = User::find($id);
        $user->fill($request->all());
        $user->save();
        Session::flash('message','Usuario Editado Correctamente');
        return Redirect::to('/usuario');*/
    }
}
