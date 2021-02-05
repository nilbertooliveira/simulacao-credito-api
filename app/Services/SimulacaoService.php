<?php

namespace App\Services;

use App\Repositories\Contracts\SimulacaoRepositoryInterface;
use App\Validators\SimulacaoValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class SimulacaoService
{
    protected $repository;

    public function __construct(SimulacaoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getInstituicoes()
    {
        return $this->repository->getInstituicoes();
    }

    public function getConvenios()
    {
        return $this->repository->getConvenios();
    }

    public function getSimulacoes(array $parametros)
    {
        $parametros = $this->upperCaseParametros($parametros);

        $validator = $this->validaParametros($parametros);

        if (isset($validator['errors'])) {
            return [
                'errors' => $validator['errors'],
            ];
        }
        $taxas = $this->repository->getTaxas($parametros);

        $simulacao = $this->calculaSimulacao($taxas, $parametros);

        return [
            'simulacoes' => $simulacao,
        ];
    }

    public function calculaSimulacao(Collection $taxas, array $parametros)
    {
        foreach ($taxas as $key => $instituicao) {
            foreach ($instituicao as $i => $inst) {
                $valorParcela = ($parametros['valor_emprestimo'] * $inst->taxaJuros) / $inst->parcelas;
                $taxas[$key][$i]->valor_parcela = round($valorParcela, 2);
            }
        }
        return $taxas;
    }

    public function validaParametros(array $parametros)
    {
        $validator = Validator::make($parametros, SimulacaoValidator::RULE);

        if ($validator->fails()) {
            return [
                'errors' => $validator->errors(),
            ];
        }
        $instParam = collect($parametros['instituicoes']);
        $instituicoes = $this->repository->getIntituicoesSemChaves();
        $diff = $instParam->diff($instituicoes);

        if (count($diff) > 0) {
            return [
                'errors' => "Instituições não cadastradas: " . $diff,
            ];
        }
        $convParam = collect($parametros['convenios']);
        $convenios = $this->repository->getConveniosSemChaves();
        $diff = $convParam->diff($convenios);

        if (count($diff) > 0) {
            return [
                'errors' => "Convenios não cadastrados: " . $diff,
            ];
        }
    }

    private function upperCaseParametros(array $parametros)
    {
        if (isset($parametros['instituicoes'])) {
            $param = array_flip($parametros['instituicoes']);
            $param = array_change_key_case($param, CASE_UPPER);
            $param = array_flip($param);
            $parametros['instituicoes'] = $param;
        }
        if (isset($parametros['convenios'])) {
            $param = array_flip($parametros['convenios']);
            $param = array_change_key_case($param, CASE_UPPER);
            $param = array_flip($param);
            $parametros['convenios'] = $param;
        }
        return $parametros;
    }

}
