<?php


namespace App\Http\Controllers;


use App\Compania;
use App\Etapas;
use App\Fase;
use App\Proyecto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;


class EtapasController
{
    public function index(){
        $compania=Compania::where('Clave',Auth::user()->Clave_Compania)->first();
        $datetime = Carbon::now();
        $datetime->setTimezone('GMT-7');
        $date = $datetime->toDateString();
        $time = $datetime->toTimeString();
        $etapa=DB::table('Etapas')
            ->leftJoin('Proyectos', 'Etapas.Clave_Proyecto', '=', 'Proyectos.Clave')
            ->leftJoin('Fases', 'Etapas.Clave_Fase', '=', 'Fases.Clave')
            ->select('Etapas.Clave','Proyectos.Descripcion as Proyecto','Fases.Descripcion as Fase','Etapas.Descripcion','Etapas.Fecha_Vencimiento','Etapas.Hora_Vencimiento', 'Etapas.created_at as Creado')
            ->where('Etapas.Clave_Compania','=',Auth::user()->Clave_Compania)
            ->orderBy('Creado', 'asc')
            ->get();
        return view('Admin.Etapas.index',['etapa'=>$etapa,'compania'=>$compania, 'date'=>$date, 'time'=>$time]);
    }

    public function edit($id){
        $etapa=Etapas::where('Clave', $id)->get()->toArray();
        $etapaId = $etapa[0]['Clave'];
        $etapa = $etapa[0];
        $company = Compania::all();
        $etapaCompany = $etapa['Clave_Compania'];
        $convertedtime = date("G:i:s", strtotime($etapa['Hora_Vencimiento']));
        return view('Admin.Etapas.edit', compact('etapa', 'etapaId', 'company', 'etapaCompany'));
    }

    public function new(){
        $compania=Compania::where('Clave',Auth::user()->Clave_Compania)->first();
        $proyectos=Proyecto::where('Clave_Compania',Auth::user()->Clave_Compania)->get();
        return view('Admin.Etapas.new', compact('proyectos'));
    }

    public function store(Request $request){
        $compania=Compania::where('Clave',Auth::user()->Clave_Compania)->first();

        $etapa = $request->validate([
            'descripcion' => ['required', 'string', 'max:150'],
            'fechaV' => ['required', 'date'],
            'horaV' => ['required'],
            'proyecto' => ['required']
        ]);

        $proyecto = Proyecto::where('Clave', $etapa['proyecto'])->first();
        $fase = Fase::where('Clave', $proyecto->Clave_Fase)->first();
        $faseId = $fase->Clave;
        $companyId = $fase->Clave_Compania;

        Etapas::create([
            'Descripcion' => $etapa['descripcion'],
            'Fecha_Vencimiento' => $etapa['fechaV'],
            'Hora_Vencimiento' => $etapa['horaV'],
            'Clave_Proyecto' => $etapa['proyecto'],
            'Clave_Compania' => $companyId,
            'Clave_Fase' => $faseId
        ]);
        return redirect('/Admin/Etapas')->with('mensaje', "Nueva etapa agregada correctamente");
    }

    public function prepare($id){
        $etapa=Etapas::where('Clave', $id)->get()->toArray();
        $etapa = $etapa[0];
        return view('Admin.Etapas.delete', compact('etapa'));
    }

    public function delete($id){
        $etapa = Etapas::find($id);
        $etapa->delete();
        return redirect('/Admin/Etapas')->with('mensajeAlert', "Etapa eliminada correctamente");
    }

    public function update(Request $request, $Clave){
        $etapa = Etapas::where('Clave', $Clave)->firstOrFail();
        $etapaNew = $request->input('descripcion');
        $fechaVNew = $request->input('fechaV');
        $horaVNew = $request->input('horaV');
        $convertedtime = date("G:i:s", strtotime($horaVNew));

        if ($etapaNew == $etapa->Descripcion) {
            if ($fechaVNew == $etapa->Fecha_Vencimiento) {
                if ($convertedtime == $etapa->Hora_Vencimiento) {
                    return redirect('/Admin/Etapas')->with('mensajeAlert', "No hubo datos nuevos");
                }
                else {
                    $etapa = $request->validate([
                        'horaV' => ['required']
                    ]);
                    Etapas::where('Clave', $Clave)->update([
                        'Hora_Vencimiento' => $etapa['horaV']
                    ]);
                }
            }
            else if ($convertedtime == $etapa->Hora_Vencimiento){
                $etapa = $request->validate([
                    'fechaV' => ['required', 'date']
                ]);
                Etapas::where('Clave', $Clave)->update([
                    'Fecha_Vencimiento' => $etapa['fechaV']
                ]);
            }
            else {
                $etapa = $request->validate([
                    'horaV' => ['required'],
                    'fechaV' => ['required', 'date']
                ]);
                Etapas::where('Clave', $Clave)->update([
                    'Hora_Vencimiento' => $etapa['horaV'],
                    'Fecha_Vencimiento' => $etapa['fechaV']
                ]);
            }
        }
        else if ($fechaVNew == $etapa->Fecha_Vencimiento) {
            if ($convertedtime == $etapa->Hora_Vencimiento) {
                $etapa = $request->validate([
                    'descripcion' => ['required', 'string', 'max:150']
                ]);
                Etapas::where('Clave', $Clave)->update([
                    'Descripcion' => $etapa['descripcion']
                ]);
            }
            else {
                $etapa = $request->validate([
                    'descripcion' => ['required', 'string', 'max:150'],
                    'horaV' => ['required']
                ]);
                Etapas::where('Clave', $Clave)->update([
                    'Hora_Vencimiento' => $etapa['horaV'],
                    'Descripcion' => $etapa['descripcion']
                ]);
            }
        }
        else if ($convertedtime == $etapa->Hora_Vencimiento) {
            $etapa = $request->validate([
                'horaV' => ['required']
            ]);
            Etapas::where('Clave', $Clave)->update([
                'Hora_Vencimiento' => $etapa['horaV']
            ]);
        }
        else {
            $etapa = $request->validate([
                'descripcion' => ['required', 'string', 'max:150'],
                'horaV' => ['required'],
                'fechaV' => ['required', 'date']
            ]);
            Etapas::where('Clave', $Clave)->update([
                'Hora_Vencimiento' => $etapa['horaV'],
                'Fecha_Vencimiento' => $etapa['fechaV'],
                'Descripcion' => $etapa['descripcion']
            ]);
        }
        return redirect('/Admin/Etapas')->with('mensaje', "La etapa fue editada correctamente");
    }

    public function preparePdf(Request $request) {
        $etapas = DB::table('Etapas')
            ->leftJoin('Proyectos', 'Etapas.Clave_Proyecto', '=', 'Proyectos.Clave')
            ->select('Etapas.Clave as Clave', 'Proyectos.Descripcion as Proyecto', 'Etapas.Descripcion as Etapa')
            ->where('Etapas.Clave_Compania', '=', Auth::user()->Clave_Compania)
            ->get();
        $compania=Compania::where('Clave',Auth::user()->Clave_Compania)->first();
        $proyectos = Proyecto::where('Clave_Compania', Auth::user()->Clave_Compania)->get();
        $fases = Fase::where('Clave_Compania', Auth::user()->Clave_Compania)->get();

        return view('Admin.Etapas.prepare', compact('proyectos', 'fases', 'etapas', 'compania'));
    }

    public function exportPdf(Request $request)
    {
        $proyectos = $request->input('proyectos');
        $etapas2 = $request->input('etapas');
        $fases = $request->input('fases');
        $datetime = Carbon::now();
        $datetime->setTimezone('GMT-7');
        $date = $datetime->toDateString();
        $time = $datetime->toTimeString();

        $etapas = DB::table('Etapas')
            ->where(function($query) use ($etapas2, $request) {
                if ($etapas2 != null) {
                    $query->whereIn('Etapas.Clave', $etapas2);
                }
            })
            ->join('Companias', 'Etapas.Clave_Compania', '=', 'Companias.Clave')
            ->where('Etapas.Clave_Compania', '=', Auth::user()->Clave_Compania)
            ->join('Proyectos', 'Etapas.Clave_Proyecto', '=', 'Proyectos.Clave')
            ->where(function($query) use ($proyectos, $request) {
                if ($proyectos != null) {
                    $query->whereIn('Etapas.Clave_Proyecto', $proyectos);
                }
            })
            ->join('Fases', 'Etapas.Clave_Fase', '=', 'Fases.Clave')
            ->where(function($query) use ($fases, $request) {
                if ($fases != null) {
                    $query->whereIn('Etapas.Clave_Fase', $fases);
                }
            })
            ->select('Etapas.*', 'Companias.Descripcion as Compania', 'Fases.Descripcion as Fase', 'Proyectos.Descripcion as Proyecto')
            ->get();

        $pdf = PDF::loadView('pdf.stages', compact('etapas', 'date', 'time'));

        return $pdf->download('etapas.pdf');
    }
}
