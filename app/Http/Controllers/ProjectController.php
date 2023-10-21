<?php

namespace App\Http\Controllers;

use App\Http\Resources\V1\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
/**
 * Classe responsavel pelo controller dos projetos
 * @version ${2:2.0.0
 */
class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected Project $project;
     public function __construct() {
         $this->project = new Project;
     }
    public function index() {
        $projects = $this->project->paginate();
        return ProjectResource::collection($projects);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $data = $request->all();
        return $this->project->createProjects($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id) {   
        $p = $this->project->getByName($id);
        return $p;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();
        $this->project->updateProjects($id, $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
       $delete = $this->project->deleteProjects($id);
    }
}
