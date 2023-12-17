<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
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

    public function store(StoreCommentRequest $request) {
        $data = $request->validated();
        try {
           CustomException::actionException($this->repository->createComment($data));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage() ], 403);
        }
    }

    public function destroy(string $comment) {
        try {
            CustomException::actionException($this->repository->deleteComment($comment));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage() ], 403);
        }
    }

    public function replyStore()
    {
        
    }
}