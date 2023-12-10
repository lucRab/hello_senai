<?php

namespace App\Filters\V1;

use App\Filters\ApiFilters;

class ProjectsFilter extends ApiFilters
{
  protected $safeParams = [
    'nomeProjeto' => ['eq', 'lk']
  ];

  protected $columnMap = [
    'nomeProjeto' => 'nome_projeto'
  ];

  protected $operatorMap = [
    'eq' => '=', // IGUAL
    'lt' => '<', // MENOR QUE
    'lte' => '<=', // MENOR OU IGUAL QUE
    'gt' => '>', // MAIOR QUE
    'gte' => '>=', // MAIOR OU IGUAL QUE
    'ne' => '!=',
    'lk' => 'LIKE' // DIFERENTE QUE
  ];
}