<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Comment;
use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\CreateCommentRequest;
use App\Http\Requests\Comment\ReplyToCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $comments = Comment::whereNull('parent_id')
            ->latest('created_at');

        return response()->json(Utils::paginate($comments));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Article $article
     * @param CreateCommentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Article $article, CreateCommentRequest $request)
    {
        $comment = Comment::create([
            'article_id' => $article->id,
            'user_id' => auth()->id(),
            'content' => $request->input('content')
        ]);

        $comment = Comment::find($comment->id);

        return response()->json($comment);
    }

    /**
     * Display the specified resource.
     *
     * @param  Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Comment $comment)
    {
        return response()->json($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Comment $comment, UpdateCommentRequest $request)
    {
        if (!$comment->user->isAuthUser()) {
            return response()->json('Forbidden', 403);
        }

        $comment->update($request->only(['content'])) ;

        return response()->json($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Comment $comment)
    {
        if (!$comment->user->isAuthUser()) {
            return response()->json('Forbidden', 403);
        }

        $comment->delete();

        return response()->json($comment, 200);
    }

    public function getArticleComments(Article $article)
    {
        $comments = $article->comments()
            ->whereNull('parent_id')
            ->where('article_id', $article->id)
            ->latest('created_at');

        return response()->json(Utils::paginate($comments));
    }

    public function getReplies(Comment $comment)
    {
        $comments = Comment::where('parent_id', $comment->id)
            ->latest('created_at');

        return response()->json(Utils::paginate($comments));
    }

    public function replyToComment(Comment $comment, ReplyToCommentRequest $request)
    {
        $reply = Comment::create([
            'user_id' => auth()->id(),
            'article_id' => $comment->article_id,
            'parent_id' => $comment->id,
            'content' => $request->input('content')
        ]);

        $reply = Comment::find($reply->id);

        return response()->json($reply);
    }
}
