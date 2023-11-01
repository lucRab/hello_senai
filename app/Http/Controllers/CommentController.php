<?php

namespace App\Http\Controllers;

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
    public function store(Request $request)
    {
        $data = $request->all();

        $this->repository->createComment($data);
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
    public function update(Request $request, string $comment)
    {
        $data = $request->all();
        $this->repository->updateComment($comment, $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $comment)
    {
        $this->repository->deleteComment($comment);
    }
}
