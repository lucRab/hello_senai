<?php

namespace App\Http\Controllers;

use App\Services\CustomExcepition;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;

class TeacherController extends Controller
{
    public function __construct(
        protected Teacher $repository
    )
    {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherRequest $request)
    {   
        try {
            $user = Auth::guard('sanctum')->user();
            CustomExcepition::authorizedActionException( 'teacher-store', $user);
            
            $data = $request->validated();
            $data['senha'] = bcrypt($request->senha);
            CustomExcepition::actionExcepition($this->repository->createTeacher($data));

            return response()->json(['message' => 'Professor Criado Com Sucesso', 200]);
        }catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 403); 
        } 
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        //
    }
}