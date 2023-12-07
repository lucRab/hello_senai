<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Services\CustomException;
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
     * Store a newly created resource in storage.
     */
    public function store(CommentRequest $request) {
        //valida os dados recebidos
        $data = $request->validated();
        try {
            //verifica se a ação feita não deu erro
           CustomException::actionException($this->repository->createComment($data));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage() ], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, string $comment) {
        //valida os dados recebidos
        $data = $request->validated();
        try {
            //verifica se a ação feita não deu erro
            CustomException::actionException($this->repository->updateComment($comment, $data));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage() ], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $comment) {
        try {
            //verifica se a ação feita não deu erro
            CustomException::actionException($this->repository->deleteComment($comment));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage() ], 403);
        }
    }
}
