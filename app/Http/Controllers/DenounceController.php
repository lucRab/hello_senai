<?php

namespace App\Http\Controllers;

use App\Models\Denounce;
use App\Http\Requests\StoreDenounceRequest;
use App\Http\Requests\UpdateDenounceRequest;
use App\Http\Resources\V1\DenounceResource;
use Auth;
use Illuminate\Http\Request;


class DenounceController extends Controller
{
    public function __construct(
        protected Denounce $repository
    ) 
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (Auth::guard('sanctum')->check()) {
            $searchQueryLimit = $request->query('limit') ?: 15;
            $denounces = $this->repository->with('project')->paginate($searchQueryLimit);
            return DenounceResource::collection($denounces);
        }
        return response()->json(['message' => 'Autorização negada'], 401);
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
    public function store(StoreDenounceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Denounce $denounce)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Denounce $denounce)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDenounceRequest $request, Denounce $denounce)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Denounce $denounce)
    {
        //
    }
}