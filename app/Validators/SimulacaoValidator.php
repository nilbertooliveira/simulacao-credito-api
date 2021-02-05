<?php

namespace App\Validators;

class SimulacaoValidator
{

    const RULE = [
        'valor_emprestimo' => 'required|numeric',
        'instituicoes' => 'array',
        'convenios' => 'array',
        'parcelas' => 'integer|min:36|max:84',
    ];
}
