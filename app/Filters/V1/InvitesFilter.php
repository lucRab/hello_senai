<?php

namespace App\Filters\V1;

use App\Filters\ApiFilters;

class InvitesFilter extends ApiFilters
{
  protected $safeParams = [
    'titulo' => ['eq', 'lk']
  ];

  protected $columnMap = [];

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