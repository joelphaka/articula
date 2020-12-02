<?php


namespace App\Http\Controllers\Api;

use App\Article;
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

            //return response()->json($like->likeable());
        }

        //return response()->json(['message' => 'Article already liked'], 403);

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

        ///return response()->json(['message' => 'Article not liked'], 403);
    }
}
