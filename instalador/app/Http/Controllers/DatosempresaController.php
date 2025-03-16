<?php

namespace Selectra_planilla\Http\Controllers;

use Illuminate\Http\Request;

use Selectra_planilla\Http\Requests;
use Selectra_planilla\Http\Controllers\Controller;
use Selectra_planilla\Empresa;
use Selectra_planilla\Datosempresa;
use Selectra_planilla\Pais;
use Session;
use Redirect;

class DatosempresaController extends Controller
{
    public function index()
    {
    	$empresas = Datosempresa::paginate(5);
        return view('empresa.index',compact('empresas'));
    }
    public function create($id)
    {
    	return view('empresa.create');
    }
    public function store(Request $request)
    {
        Datosempresa::create($request->all());
        return redirect('/empresa')->with('message','store');
    }
    public function show($id)
    {
        $datosempresa = Datosempresa::find($id);
        $paises = Pais::all();
        if (empty($datosempresa))
        {
            $datosempresa = Empresa::find($id);
            Datosempresa::create([
            'cod_empresa' => $datosempresa->codigo,
            'nombre_empresa' => $datosempresa->nombre,
            ]);
        }
        /*
        $data = [
            'cod_empresa' => $datosempresa->cod_empresa,
            'nombre_empresa' => $datosempresa->nombre_empresa,
            'direccion' => $datosempresa->direccion,
            'telefonos' => $datosempresa->telefonos,
            'rif' => $datosempresa->rif,
            'nombre_sistema' => $datosempresa->nombre_sistema,
            'pais_id' => $paises,
                ];
        */
        return view('empresa.editdatos',['datosempresa'=>$datosempresa,'paises'=>$paises]);
    }
    public function adddatos($id)
    {
    	$datosempresa = Datosempresa::find($id);
        return view('empresa.adddatos',['datosempresa'=>$datosempresa]);
    }
    public function edit($id)
    {
    	$empresa = Datosempresa::find($id);
        return view('empresa.edit',['empresa'=>$empresa]);
    }
    public function update($id, Request $request)
    {
    	$empresa = Datosempresa::find($id);
        $empresa->fill($request->all());
        $empresa->save();
        Session::flash('message','Empresa Editada Correctamente');
        return Redirect::to('/empresa');
    }
    public function destroy($id)
    {
    	$empresa=Datosempresa::find($id);
        $empresa->delete();
        Session::flash('message','Empresa Eliminada Correctamente');
        return Redirect::to('/empresa');
    }
}
