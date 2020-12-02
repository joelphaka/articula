<?php


namespace App\Http\Controllers\Api;

use App\Article;
use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\User;
use Illuminate\Http\Request;


class ProfileController extends Controller
{
    public function index(User $user)
    {
        return response()->json($user);
    }

    public function articlesTimeline(User $user)
    {
        $articles = $user->articles()->sortBy(
            request('sort_by'),
            request('sort_direction')
        );

        return response()->json(Utils::paginate($articles));
    }
}
