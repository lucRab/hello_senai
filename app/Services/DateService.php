<?php

namespace App\Services;

use Carbon\Carbon;

class DateService {

  public static function transformDateHumanReadable(string $date) 
  {
    date_default_timezone_set('America/Sao_Paulo');
    setlocale(LC_ALL, 'pt_BR.utf-8', 'ptb', 'pt_BR', 'portuguese-brazil', 'portuguese-brazilian', 'bra', 'brazil', 'br');
    setlocale(LC_TIME, 'pt_BR.utf-8', 'ptb', 'pt_BR', 'portuguese-brazil', 'portuguese-brazilian', 'bra', 'brazil', 'br');
    
    $date = Carbon::parse($date);
    $currentDate = Carbon::now();

    if ($date->isSameDay($currentDate)) {
        return 'Hoje';
    } elseif ($date->isPast()) {
      if ($date->year < $currentDate->year)
      {
        return $date->formatLocalized('%d %b %Y');
      } else {
        return $date->formatLocalized('%d %b');
      }
    } else {
      return $date->diffForHumans();
    }
  }

}