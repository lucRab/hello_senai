<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Project;

class ProjectService extends SlugService
{
    public function getProjectBySlug($slug)
    {
      if ($data = Project::with('user')->where('slug', '=', $slug)->first())
      {
        return $data;
      };
      return null;
    }
}