<?php

namespace App\Http\Controllers;

use App\Services\SimulacaoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SimulacaoController extends Controller
{
    protected $service;

    public function __construct(SimulacaoService $service)
    {
        $this->service = $service;
    }

    public function getInstituicoes()
    {
        try {
            return response()->json($this->service->getInstituicoes());
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function getConvenios()
    {
        try {
            return response()->json($this->service->getConvenios());
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function getSimulacoes(Request $request)
    {
        try {
            return response()->json($this->service->getSimulacoes($request->all()));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
