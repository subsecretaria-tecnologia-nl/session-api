<?php

namespace App\Http\Controllers;

use App\Exceptions\ShowableException;
use App\Models\Divisa;
use Illuminate\Http\Request;

class DivisasController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getDivisas(){
        $divisas = array(
        array(
            "parametro"=>"SF43718",
            "descripcion"=>"Pesos por Dólar. FIX."
        ),
        array(
            "parametro"=>"SF60653",
            "descripcion"=>"Pesos por Dólar. Fecha de liquidación."
        ), 
        array(
            "parametro"=>"SF46410",
            "descripcion"=>"Euro."
        ),
        array(
            "parametro"=>"SF46406",
            "descripcion"=>"Yen japónes."
        ),
        array(
            "parametro"=>"SF46407",
            "descripcion"=>"Libra esterlina."
        ),
        array(
            "parametro"=>"SF60632",
            "descripcion"=>"Dólar Canadiense."
        ),
        array(
            "parametro"=>"SP68257",
            "descripcion"=>"Valor de UDIS."
        ),
        array(
            "parametro"=>"SF290383",
            "descripcion"=>"Cotización de las divisas que conforman la canasta del DEG 1/ 
            y del DEG respecto al Peso mexicano 2/ Yuan chino."
        ));

        return $divisas;

    }
    public function saveDivisas(Request $request){
        $error =null;
        $divisas = to_object($request->divisas);
        
        foreach ($divisas as $value) {
         
            try {
                $divisa=Divisa::create([
                        "descripcion" => $value->descripcion,
                        "parametro" => $value->parametro
                ]);
            } catch (\Exception $e) {
                $error = $e;
            }          
    
        }

        if($error) throw $error;

		return [
			'success' => true,
		    'status'=> 200
		];
    }

    public function deleteDivisas(Request $request){
        $error =null;
        $divisas = to_object($request->divisas);
        
        foreach ($divisas as $value) {
         
            try {
                $divisa=Divisa::where('parametro', $value->parametro)->delete();
            } catch (\Exception $e) {
                $error = $e;
            }          
    
        }

        if($error) throw $error;

		return [
			'success' => true,
		    'status'=> 200
		];
    }

    public function getDivisasSave(){
        $divisas = Divisa::get();
		return [
			"divisas" => $divisas->toArray()
		];
    }

		
}
