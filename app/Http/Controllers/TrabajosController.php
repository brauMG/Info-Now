<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Compania;
use App\Trabajo;
class TrabajosController extends Controller
{ 
    public function __construct(){
        $this->middleware('auth');
    }
    public function index(){
        if(Auth::user()->Clave_Rol==1 ){
            $trabajo=Trabajo::all();
            $compania=Compania::where('Clave',Auth::user()->Clave_Compania)->first();
            return view('Admin.Trabajos.index',['trabajo'=>$trabajo,'compania'=>$compania]);
        }else{
            return redirect('/');
        }
    }
    public function edit($id){
        if(Auth::user()->Clave_Rol==1 ){
            $trabajo=Trabajo::find($id);
            return view('Admin.Trabajos.edit',['trabajo'=>$trabajo]);
        }
        else
        {
            return redirect('/');
        }
    }

    public function new(){        
        if(Auth::user()->Clave_Rol==1 ){
            return view('Admin.Trabajos.new');
        }
        else{
            return redirect('/');
        }
    }
    public function create(Request $request){
        $trabajo = new Trabajo;
        $trabajo->Descripcion = $request->descripcion;
        $trabajo->Activo=true;
        $trabajo->save();
        return response()->json(['trabajo'=>$trabajo]);
    }
    public function delete($id){
        $trabajo = Trabajo::find($id);
        $trabajo->delete();
        return response()->json(['error'=>false]);
    }
    public function update(Request $request){
        $trabajo = Trabajo::find($request->clave);
        $trabajo->Descripcion = $request->descripcion;
        $trabajo->Activo=true;
        $trabajo->save();
        return response()->json(['trabajo'=>$trabajo]);
    }    
}
