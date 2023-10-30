<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class SlugService
{
    public function generateSlug($name)
    {
      $slug = DB::select("SELECT createUniqueSlug(?) AS slug", [$name])[0]->slug;
      return $slug;
    }
}