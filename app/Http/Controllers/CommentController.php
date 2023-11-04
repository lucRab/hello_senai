<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    
    
    public function __construct(
        protected Comment $repository
    ) {
        $this->repository = new Comment();
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
    public function store(CommentRequest $request) {
        $data = $request->validated();
        if(!$this->repository->createComment($data))
        return response()->json(['message' => 'Não Foi Possível Realizar Essa Ação'], 403);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, string $comment) {
        $data = $request->validated();
        if(!$this->repository->updateComment($comment, $data))
        return response()->json(['message' => 'Não Foi Possível Realizar Essa Ação'], 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $comment) {
        if(!$this->repository->deleteComment($comment))
        return response()->json(['message' => 'Não Foi Possível Realizar Essa Ação'], 403);
    }
}
