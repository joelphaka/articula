<?php


namespace App\Http\Controllers\Api;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function index(Request $request)
    {
        return $request->user();
    }

    public function update(UpdateUserRequest $request)
    {
        $data = Utils::extractNonNull($request->only([
            'first_name',
            'last_name',
            'gender',
            'bio'
        ]));

        if (count($data)) auth()->user()->update($data);

        return response()->json(auth()->user(), 200);
    }

    public function destroy(Request $request)
    {
        return $request->user();
    }
}
