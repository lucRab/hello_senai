<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectService implements ISlugService
{
    public function generateSlug($name)
    {
        $slug = DB::select("SELECT createUniqueSlug(?, ?) AS slug", [$name, 'projeto'])[0]->slug;
        return $slug;
    }
    
    public function getBySlug($slug)
    {
        $data = Project::with(['user', 'participants', 'comments.user','comments.reply.user'])->where('slug', '=', $slug)->first();
        $data->comments = $data->comments->filter(function ($comment) use ($data) {
            return !$data->comments->where('idresposta', $comment->idcomentario)->isNotEmpty();
        });
        return $data;
    }

    public function isProjectStored($slug)
    {
        $project = Project::where('slug', '=', $slug)->first();
        return $project;
    }
}