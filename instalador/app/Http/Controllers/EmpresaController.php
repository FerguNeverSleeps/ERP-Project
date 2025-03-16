<?php

namespace Selectra_planilla\Http\Controllers;

use Illuminate\Http\Request;

use Selectra_planilla\Http\Requests;
use Selectra_planilla\Http\Controllers\Controller;
use Selectra_planilla\Empresa;
use Selectra_planilla\Datosempresa;
use Selectra_planilla\Pais;
use Input,Session,Redirect;
class EmpresaController extends Controller
{
    public function index()
    {
    	$conexion= mysqli_connect('localhost','root','');
        $sentencia='CREATE DATABASE IF NOT EXISTS nombre_de_la_base';
        mysqli_query($conexion, $sentencia);
        $empresas = Empresa::paginate(5);
        return view('empresa.index',compact('empresas'));
    }
    public function create()
    {
        $paises = Pais::all();
        return view('empresa.create',['paises'=>$paises]);
    }
    public function store(Request $request)
    {
        $nomempresa = [
            'nombre' => $request->nombre,
            'bd_nomina' => $request->bd_nomina,
                ];
        Empresa::create($nomempresa);
        $img_izq="logo_prueba.jpg";
        $datos_empresa = [
            'nombre_empresa' => $request->nombre,
            'img_izq' => $img_izq,
            'nombre_sistema' => $request->nombre_sistema,
            'pais_id' => $request->pais_id,
                ];
        Datosempresa::create($datos_empresa);
        return redirect('/empresa')->with('message','store');
    }
    public function show($id)
    {
        $empresa = Empresa::find($id);
        $datosempresa = Datosempresa::find($id);
        $paises = Pais::all();
        return view('empresa.edit',['empresa'=>$empresa,'datosempresa'=>$datosempresa,'paises'=>$paises]);
    }
    public function edit($id)
    {
    	$empresa = Empresa::find($id);
        return view('empresa.edit',['empresa'=>$empresa]);
    }
    public function datos($id)
    {
        $empresa = Datosempresa::find($id);
        return view('empresa.datos',['empresa'=>$empresa]);
    }
    public function update($id, Request $request)
    {
    	$empresa = Empresa::find($id);
        $empresa->fill($request->all());
        $empresa->save();

        $data = [
            'nombre_empresa' => $request->nombre,
            'nombre_sistema' => $request->nombre_sistema,
            'pais_id' => $request->pais_id,
                ];
        $datosempresa = Datosempresa::find($id);
        $datosempresa->fill($data);
        $datosempresa->save();
        Session::flash('message','Empresa Editada Correctamente');
        return Redirect::to('/empresa');
    }
    public function destroy($id)
    {
    	$empresa=Empresa::find($id);
        $datosempresa=Datosempresa::find($empresa->codigo);
        $empresa->delete();
        $datosempresa->delete();
        Session::flash('message','Empresa Eliminada Correctamente');
        return Redirect::to('/empresa');
    }
}
