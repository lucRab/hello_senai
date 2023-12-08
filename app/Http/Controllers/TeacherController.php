<?php

namespace App\Http\Controllers;

use App\Services\CustomException;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Http\Resources\V1\TeacherResource;
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
        $teachers = $this->repository->with(['challenge' => function($query) {
            $query->take(3);
        }, 'user'])->paginate();
        return TeacherResource::collection($teachers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherRequest $request)
    {   
        try {
            $user = Auth::guard('sanctum')->user();
            CustomException::authorizedActionException('teacher-store', $user);
            
            $data = $request->validated();
            $data['senha'] = bcrypt($request->senha);
            CustomException::actionException($this->repository->createTeacher($data));

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