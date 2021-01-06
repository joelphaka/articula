<?php


namespace App\Http\Controllers\Api;

use App\Article;
use App\Comment;
use App\Http\Controllers\Controller;
use App\Like;
use App\UsArticle;
use Illuminate\Http\Request;


class LikeController extends Controller
{
    public function likeArticle(Article $article)
    {
        if (!auth()->user()->hasLiked($article)) {
            $like = new Like([
                'likeable_type' => Article::class,
                'likeable_id' => $article->id,
                'user_id' => auth()->id()
            ]);

            $like->save();
        }

        return response()->json($article->refresh());
    }

    public function unlikeArticle(Article $article)
    {
        if (auth()->user()->hasLiked($article)) {

            $like = auth()->user()
                ->likes()
                ->where('likeable_type', Article::class)
                ->where('likeable_id', $article->id);

            $like->delete();

            return response()->json($article->refresh());
        }

        return response()->json($article->refresh());
    }

    public function likeComment(Comment $comment)
    {
        if (!auth()->user()->hasLiked($comment)) {
            $like = new Like([
                'likeable_type' => Comment::class,
                'likeable_id' => $comment->id,
                'user_id' => auth()->id()
            ]);

            $like->save();
        }

        return response()->json($comment->refresh());
    }

    public function unlikeComment(Comment $comment)
    {
        if (auth()->user()->hasLiked($comment)) {

            $like = auth()->user()
                ->likes()
                ->where('likeable_type', Comment::class)
                ->where('likeable_id', $comment->id);

            $like->delete();

            return response()->json($comment->refresh());
        }

        return response()->json($comment->refresh());
    }
}
