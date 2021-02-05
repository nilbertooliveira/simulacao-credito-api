<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface SimulacaoRepositoryInterface
{
    public function getFileInstituicoes();
    public function getFileConvenios();
    public function getFileTaxas();
    public function removeChave($collections, $chave);
    public function getInstituicoes();
    public function getConvenios();
    public function getTaxas(array $data);
    public function getIntituicoesSemChaves();
    public function getConveniosSemChaves();
    public function getGroupInstituicoesTaxas();
    public function filtraInstituicoes(Collection $collection, array $parametros);
    public function filtraConvenios(Collection $collections, array $parametros);
    public function filtraParcelas(Collection $collections, array $parametros);
}
