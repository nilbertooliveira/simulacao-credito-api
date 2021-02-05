<?php

namespace App\Repositories;

use App\Repositories\Contracts\SimulacaoRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SimulacaoRepository implements SimulacaoRepositoryInterface
{
    protected $instuicoes;
    protected $convenios;
    protected $taxas;
    protected $path_json_instuicoes = 'files_json/instituicoes.json';
    protected $path_json_convenios = 'files_json/convenios.json';
    protected $path_json_taxas = 'files_json/taxas_instituicoes.json';
    protected $simulacoes;

    public function __construct()
    {
        $this->getFileInstituicoes();
        $this->getFileConvenios();
        $this->getFileTaxas();

        Collection::macro('toUpper', function () {
            return $this->map(function ($value) {
                return Str::upper($value);
            });
        });
    }

    public function getInstituicoes()
    {
        $this->instuicoes = $this->instuicoes->sortBy('valor');
        return $this->instuicoes->values()->all();
    }

    public function getConvenios()
    {
        $this->convenios = $this->convenios->sortBy('valor');
        return $this->convenios->values()->all();
    }

    public function getTaxas(array $parametros)
    {
        $groupTaxas = $this->getGroupInstituicoesTaxas();
        $groupTaxas = $this->removeChave($groupTaxas, 'instituicao');
        $groupTaxas = $this->filtraInstituicoes($groupTaxas, $parametros);
        $groupTaxas = $this->filtraConvenios($groupTaxas, $parametros);
        $groupTaxas = $this->filtraParcelas($groupTaxas, $parametros);

        return $groupTaxas->reject(function ($value) {
            return count($value) == 0;
        });
    }

    public function getFileInstituicoes()
    {
        $this->instuicoes = Storage::disk('local')->get($this->path_json_instuicoes);
        $this->instuicoes = json_decode($this->instuicoes);
        $this->instuicoes = collect($this->instuicoes);
    }

    public function getFileConvenios()
    {
        $this->convenios = Storage::disk('local')->get($this->path_json_convenios);
        $this->convenios = json_decode($this->convenios);
        $this->convenios = collect($this->convenios);
    }

    public function getFileTaxas()
    {
        $this->taxas = Storage::disk('local')->get($this->path_json_taxas);
        $this->taxas = json_decode($this->taxas);
        $this->taxas = collect($this->taxas);
    }

    public function getConveniosSemChaves()
    {
        return $this->convenios->groupBy('valor')->keys()->toUpper();
    }

    public function getIntituicoesSemChaves()
    {
        return $this->instuicoes->groupBy('valor')->keys()->toUpper();
    }

    public function getGroupInstituicoesTaxas()
    {
        return $this->taxas->groupBy('instituicao');
    }

    public function removeChave($collections, $chave)
    {
        foreach ($collections as $i => $collection) {
            foreach ($collection as $j => $values) {
                if (isset($values->$chave)) {
                    unset($values->$chave);
                    $collections[$i][$j] = $values;
                }
            }
        }
        return $collections;
    }

    public function filtraInstituicoes(Collection $collection, array $parametros)
    {
        if (!isset($parametros['instituicoes'])) {
            return $collection;
        }
        $param = array_flip($parametros['instituicoes']);

        $intersect = $collection->intersectByKeys($param);
        $intersect->values()->all();

        return $intersect;
    }

    public function filtraConvenios(Collection $collections, array $parametros)
    {
        if (!isset($parametros['convenios'])) {
            return $collections;
        }
        foreach ($collections as $i => $collection) {
            foreach ($collection as $j => $values) {
                if (!in_array($values->convenio, $parametros['convenios'])) {
                    unset($collections[$i][$j]);
                }
            }
            $collections[$i] = $collection->values();
        }
        return $collections;
    }

    public function filtraParcelas(Collection $collections, array $parametros)
    {
        if (!isset($parametros['parcelas'])) {
            return $collections;
        }
        foreach ($collections as $i => $collection) {
            foreach ($collection as $j => $values) {
                if ($values->parcelas != $parametros['parcelas']) {
                    unset($collections[$i][$j]);
                }
            }
            $collections[$i] = $collection->values();
        }
        return $collections;
    }

}
