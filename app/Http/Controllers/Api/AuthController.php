<?php


namespace App\Http\Controllers\Api;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $http = new \GuzzleHttp\Client();

        try {
            // Authenticate to get the access token
            $tokenResponse = $http->post(config('services.passport.oauth_token_url'), [
                'form_params' => [
                    'grant_type' => config('services.passport.grant_type'),
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'username' => $request->input('email'),
                    'password' => $request->input('password'),
                    'scope' => ''
                ],
            ]);
            $tokenData = json_decode($tokenResponse->getBody()->getContents(), true);

            // Now that we have the access token, get the authenticated user with the 'access_token'
            $userResponse = $http->get(config('services.passport.auth_user_url'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$tokenData['access_token']}",
                ]
            ]);

            $userData = json_decode($userResponse->getBody()->getContents(), true);

            return response()->json(array_merge(
                $tokenData,
                ['user' => $userData]
            ));
            //return (new JsonResponse())->setJson( $response->getBody()->getContents() );

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $message = 'Something went wrong on the server.';

            if ($e->getCode() == 400) {
                $message = 'Invalid Request. Please enter an email and password.';
            } else if ($e->getCode() == 401) {
                $message = 'Your credentials are incorrect. Please try again.';
            }

            return response()->json([
                'message' => $message,
                'status_code' => $e->getCode()
            ], $e->getCode());
        }
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'username' => Utils::generateUsername( $request->input('email') ),
            'email' => $request->input('email'),
            'password' => bcrypt( $request->input('password') ),
        ]);

        return response()->json($user, 201);
    }

    public function logout()
    {
        /*
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        */

        auth()->user()->token()->revoke();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}
