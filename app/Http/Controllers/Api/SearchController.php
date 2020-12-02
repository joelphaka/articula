<?php


namespace App\Http\Controllers\Api;

use App\Article;
use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SearchController extends Controller
{
    public function searchArticles(Request $request)
    {
        $articles = Article::whereLike(['title', 'content'], $request->input('query'))
            ->sortBy(
                $request->input('sort_by') ?? 'created_at',
                $request->input('sort_direction')
            );

        return response()->json(Utils::paginate($articles));
    }

    public function searchUsers(Request $request)
    {
        $fullName = DB::raw("CONCAT(first_name, ' ',last_name)");

        $users = User::whereLike([$fullName, 'first_name', 'last_name'], $request->query('query'));
            //->orderBy('created_at', 'DESC');

        return response()->json(Utils::paginate($users));
    }
}
