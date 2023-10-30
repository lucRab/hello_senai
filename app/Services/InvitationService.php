<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Invitation;

class InvitationService extends SlugService
{
    public function getInvitationBySlug($slug)
    {
      if ($data = Invitation::with('user')->where('slug', '=', $slug)->first())
      {
        return $data;
      };
      return null;
    }
}