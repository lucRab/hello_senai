<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Challenge;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ChallengeService implements ISlugService
{
    public function generateSlug($name)
    {
      $slug = DB::select("SELECT createUniqueSlug(?, ?) AS slug", [$name, 'desafio'])[0]->slug;
      return $slug;
    }
    
    public function getBySlug($slug)
    {
      if ($data = Challenge::with('user')->where('slug', '=', $slug)->first())
      {
        return $data;
      };
      throw new HttpException(404, 'Desafio não encontrado');
    }
}