<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\CreateArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Http\Requests\Article\UploadCoverPhotoRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;


class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $articles = Article::sortBy(
            request('sort_by'),
            request('sort_direction')
        );

        return response()->json(Utils::paginate($articles));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateArticleRequest $request)
    {
        $article = new Article($request->only(['title', 'content']));
        $article->user_id = auth()->id();

        $article->save();

        $article = $this->saveCoverPhoto($request, $article);
        $article = Article::find($article->id);

        return response()->json($article, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Article $article)
    {
        $article->load('user');

        return  response()->json($article);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        // Prevent the user from updating an article that is not theirs
        if (!$article->user->isAuthUser()) {
            return response()->json('Forbidden', 403);
        }

        $data = Utils::extractNonNull($request->only(['title', 'content']));

        if (count($data)) $article->update($data);

        if ((bool)$request->input('remove_cover_photo')) {
            $article = $this->removeCoverPhoto($article);
        } else {
            $article = $this->saveCoverPhoto($request, $article);
        }

        return response()->json($article, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Article $article)
    {
        if (!$article->user->isAuthUser()) {
            return response()->json('Forbidden', 403);
        }

        $filename = $article->hashid() . '.png';

        if (Storage::disk('articles')->exists($filename)) {
            Storage::disk('articles')->delete($filename);
        }

        $article->delete();

        return response()->json($article, 200);
    }

    public function incrementViewCount(Article $article)
    {
        $article->views = !is_numeric($article->views) ? 0 : (int)$article->views;
        $article->views++;
        $article->timestamps = false;

        $article->save();

        return response()->json($article);
    }

    public function getCoverPhoto($filename)
    {
        $hashId = pathinfo($filename)['filename'];

        $exists = Article::findByHashid($hashId) && Storage::disk('articles')->exists("{$hashId}.png");

        if ($exists) {
            $image = Storage::disk('articles')->get("{$hashId}.png");

            return response($image, 200, [
                'Content-Type' => 'image/png'
            ]);
        }

        return response('Image Not Found', 404);
    }



    public function removeCoverPhoto(Article $article)
    {
        if (!$article->user->isAuthUser()) {
            return response()->json('Forbidden', 403);
        }

        $filename = $article->hashid() . '.png';

        if (Storage::disk('articles')->exists($filename)) {
            Storage::disk('articles')->delete($filename);

            $article->has_cover_photo = false;
            $article->save();
        }

        return $article;
    }

    /**
     * @param Request $request
     * @param Article $article
     * @return Article
     */
    public function saveCoverPhoto(\Illuminate\Http\Request $request, Article $article): Article
    {
        if ($request->hasFile('cover_photo')) {
            $filename = $article->hashid() . '.png';
            $path = Storage::disk('articles')->getAdapter()->getPathPrefix() . $filename;

            $request->file('cover_photo')->storeAs('/', $filename, 'articles');
            Utils::resizeImage($path);

            $article->has_cover_photo = true;
            $article->save();
        }

        return $article;
    }
}
