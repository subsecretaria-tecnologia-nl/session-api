<?php

namespace App\Http\Controllers;

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
        ));

        return $divisas;

    }

		
}
